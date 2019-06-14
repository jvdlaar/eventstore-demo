<?php

declare(strict_types=1);

namespace Infrastructure\Console;

use Amp\Loop;
use Generator;
use Infrastructure\EventStore\Projection;
use Prooph\EventStoreClient\EndPoint;
use Prooph\EventStoreClient\EventStoreConnectionFactory;
use Prooph\EventStoreClient\Exception\InvalidOperationException;
use Prooph\EventStoreClient\Exception\ProjectionCommandFailedException;
use Prooph\EventStoreClient\PersistentSubscriptionSettings;
use Prooph\EventStoreClient\Projections\ProjectionsManager;
use Prooph\EventStoreClient\UserCredentials;

final class InstallProjectionsCommand
{
    public const INSTALL_PROJECTIONS = 'install:projections';

    /**
     * @var ProjectionsManager
     */
    private $projectionsManager;

    /**
     * @var UserCredentials
     */
    private $credentials;

    /**
     * @var EndPoint
     */
    private $endPoint;

    /**
     * @var Projection[]
     */
    private $projections;

    /**
     * @param ProjectionsManager $projectionsManager
     * @param UserCredentials    $credentials
     * @param EndPoint           $endPoint
     * @param Projection[]       $projections
     */
    public function __construct(
        ProjectionsManager $projectionsManager,
        UserCredentials $credentials,
        EndPoint $endPoint,
        Projection ...$projections
    )
    {
        $this->projectionsManager = $projectionsManager;
        $this->credentials = $credentials;
        $this->endPoint = $endPoint;
        $this->projections = $projections;
    }

    public function __invoke(): void
    {
        Loop::run(function(): Generator
        {
            $connection = EventStoreConnectionFactory::createFromEndPoint(
                $this->endPoint,
                null,
                uniqid('install-', true)
            );
            yield $connection->connectAsync();

            foreach ($this->projections as $projection) {
                try {
                    yield $this->projectionsManager->createContinuousAsync(
                        $projection->getStream(),
                        $projection->getQuery(),
                        false,
                        $this->credentials
                    );
                    echo "Projection {$projection->getStream()} created\n";
                } catch (ProjectionCommandFailedException $e) {
                    echo "Projection already exists\n";
                }

                if ($projection->isPersistentSubscription()) {
                    try {
                        yield $connection->createPersistentSubscriptionAsync(
                            $projection->getStream(),
                            $projection->getGroupName(),
                            PersistentSubscriptionSettings::create()
                                ->startFromBeginning()
                                ->resolveLinkTos()
                                ->build(),
                            $this->credentials
                        );
                        echo "Persistent subscription {$projection->getStream()} created\n";
                    } catch (InvalidOperationException $e) {
                        echo "Persistent subscription {$projection->getStream()} already exists\n";
                    }
                }
            }
            Loop::stop();
        });
    }
}
