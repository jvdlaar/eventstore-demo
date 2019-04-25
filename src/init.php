<?php
declare(strict_types=1);

use Application\Commands\AddTopping;
use Application\Commands\AddToppingHandler;
use Application\Commands\CommandBus as CommandBusInterface;
use Application\Commands\CreatePizza;
use Application\Commands\CreatePizzaHandler;
use Application\Commands\IncreasePrice;
use Application\Commands\IncreasePriceHandler;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Domain\PizzaRepository;
use EventSauce\EventSourcing\ConstructingAggregateRootRepository;
use EventSauce\EventSourcing\DefaultHeadersDecorator;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\EventSourcing\Serialization\MessageSerializer;
use Http\Client\Curl\Client;
use Http\Client\HttpClient;
use Infrastructure\CommandBus\CommandBus;
use Infrastructure\CommandBus\CommandHanderFactory;
use Infrastructure\Console\AddToppingCommand;
use Infrastructure\Console\ConsoleHandlerFactory;
use Infrastructure\Console\CreatePizzaCommand;
use Infrastructure\Console\InstallDatabaseCommand;
use Infrastructure\Console\InstallProjectionsCommand;
use Infrastructure\Console\PriceCalculationConsumeHandler;
use Infrastructure\Console\PriceCalculationConsumer;
use Infrastructure\EventStore\EventsReader;
use Infrastructure\EventStore\EventStoreMessageRepository;
use Infrastructure\EventStore\EventWriter;
use Infrastructure\EventStore\Pizza\EventSourcedPizza;
use Infrastructure\EventStore\Pizza\EventSourcedPizzaRepository;
use Infrastructure\EventStore\Pizza\PizzaProjection;
use Infrastructure\EventStore\RequestFactory;
use Infrastructure\EventStore\StripPrefixClassNameInflector;
use Infrastructure\ReadModels\DbalPizzaReadModel;
use League\Container\Container;
use League\Container\ReflectionContainer;
use Nyholm\Psr7\Factory\HttplugFactory;
use Prooph\EventStoreClient\EndPoint;
use Prooph\EventStoreClient\Projections\ProjectionsManager;
use Prooph\EventStoreClient\UserCredentials;

/**
 * Set DI.
 */
$container = new Container();
$container->delegate(
    new ReflectionContainer()
);

$container->add(CommandBusInterface::class, CommandBus::class)
    ->addArgument(CommandHanderFactory::class)
;

/**
 * Set Command / CommandHandler mapping.
 */
$container->add(CommandHanderFactory::class, function() use ($container) {
    $factory = new CommandHanderFactory($container);
    $factory->registerHandler(AddTopping::class, AddToppingHandler::class);
    $factory->registerHandler(CreatePizza::class, CreatePizzaHandler::class);
    $factory->registerHandler(IncreasePrice::class, IncreasePriceHandler::class);
    return $factory;
});

/**
 * Set Console / ConsoleHandler mapping.
 */
$container->add(ConsoleHandlerFactory::class, function() use ($container) {
    $factory = new ConsoleHandlerFactory($container);
    $factory->registerHandler(InstallProjectionsCommand::INSTALL_PROJECTIONS, InstallProjectionsCommand::class);
    $factory->registerHandler(InstallDatabaseCommand::INSTALL_DATABASE, InstallDatabaseCommand::class);
    $factory->registerHandler(CreatePizzaCommand::CREATE_PIZZA, CreatePizzaCommand::class);
    $factory->registerHandler(AddToppingCommand::ADD_TOPPING, AddToppingCommand::class);
    $factory->registerHandler(PriceCalculationConsumer::CALCULATE_PRICE, PriceCalculationConsumer::class);
    return $factory;
});

/**
 * Configure aggregate root repository
 */
$container->add('pizza.eventsauce.message', EventStoreMessageRepository::class)
    ->addArgument(EventsReader::class)
    ->addArgument(EventWriter::class)
    ->addArgument('PizzaV1')
;

$container->add('pizza.eventsauce.repo', function() use ($container) {
    return new ConstructingAggregateRootRepository(
        EventSourcedPizza::class,
        $container->get('pizza.eventsauce.message'),
        null,
        new DefaultHeadersDecorator(
            $container->get(StripPrefixClassNameInflector::class)
        )
    );
});

$container->add(PizzaRepository::class, EventSourcedPizzaRepository::class)
    ->addArgument('pizza.eventsauce.repo');

/**
 * Configure event store connection
 */
$container->add(RequestFactory::class)
    ->addArgument(sprintf('http://%s:%d', getenv('EVENT_STORE_HOST'), getenv('EVENT_STORE_HTTP_PORT')))
    ->addArgument(HttpClient::class)
;

$container->add(HttpClient::class, Client::class)
    ->addArgument(HttplugFactory::class)
    ->addArgument(HttplugFactory::class)
;

$container->add(StripPrefixClassNameInflector::class)
    ->addArgument('Infrastructure\\EventStore\\Pizza\\EventSourced')
;

$container->add(MessageSerializer::class, ConstructingMessageSerializer::class)
    ->addArgument(StripPrefixClassNameInflector::class)
;

$container->add('endpoint.tcp', EndPoint::class)
    ->addArgument(getenv('EVENT_STORE_HOST'))
    ->addArgument(getenv('EVENT_STORE_TCP_PORT'))
;

$container->add(UserCredentials::class)
    ->addArgument(getenv('EVENT_STORE_USER'))
    ->addArgument(getenv('EVENT_STORE_PASS'))
;

$container->add('endpoint.http', EndPoint::class)
    ->addArgument(getenv('EVENT_STORE_HOST'))
    ->addArgument((int) getenv('EVENT_STORE_HTTP_PORT'))
;

$container->add(ProjectionsManager::class)
    ->addArgument('endpoint.http')
    ->addArgument(5000)
;

$container->add(InstallProjectionsCommand::class)
    ->addArgument(ProjectionsManager::class)
    ->addArgument(UserCredentials::class)
    ->addArgument('endpoint.tcp')
    ->addArgument(PizzaProjection::class)
;

/**
 * Configure database
 */
$container->add(InstallProjectionsCommand::class)
    ->addArgument(Connection::class)
;

$container->add(DbalPizzaReadModel::class)
    ->addArgument(Connection::class)
;

$container->add(Connection::class, function() {
    return DriverManager::getConnection([
        'driver' => 'pdo_pgsql',
        'host' => getenv('POSTGRES_HOST'),
        'port' => getenv('POSTGRES_PORT'),
        'user' => getenv('POSTGRES_USER'),
        'password' => getenv('POSTGRES_PASS'),
        'dbname' => getenv('POSTGRES_DB'),
    ]);
});

/**
 * Price calculation consumer
 */
$container->add(PriceCalculationConsumer::class)
    ->addArgument('endpoint.tcp')
    ->addArgument(PizzaProjection::class)
    ->addArgument(PriceCalculationConsumeHandler::class)
;

$container->add(PriceCalculationConsumeHandler::class)
    ->addArgument(CommandBus::class)
;

// MUST HAVE
// @todo projection
    // @todo create table
    // @todo save rows
    // @todo command for projection
    // @todo show things
// @todo assertions

// COULD HAVE
// @todo causation id + correlation id
// @todo idempotency check
// @todo separate library for event sourcing
// @todo remove eventsauce?
// @todo phpunit tests eventstore implementation
// @todo projection in nodejs
