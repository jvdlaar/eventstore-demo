<?php

declare(strict_types=1);

namespace Infrastructure\Console;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

final class InstallDatabaseCommand
{
    public const INSTALL_DATABASE = 'install:database';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function __invoke(): void
    {
        $schema = new Schema();

        $pizzaTable = $schema->createTable('pizzas');
        $pizzaTable->addColumn('pizza_id', Type::GUID, []);
        $pizzaTable->addColumn('name', Type::STRING, ['length' => 255]);
        $pizzaTable->addColumn('price', Type::SMALLINT, ['unsigned' => true, 'notnull' => false]);
        $pizzaTable->addColumn('toppings', Type::SIMPLE_ARRAY, ['notnull' => false]);
        $pizzaTable->setPrimaryKey(['pizza_id']);

        $this->connection->getSchemaManager()->dropAndCreateTable($pizzaTable);
    }
}
