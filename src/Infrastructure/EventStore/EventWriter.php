<?php

declare(strict_types=1);

namespace Infrastructure\EventStore;

use EventSauce\EventSourcing\Header;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\Serialization\MessageSerializer;
use Http\Client\Exception;
use Http\Client\HttpClient;
use Ramsey\Uuid\Uuid;
use RuntimeException;

final class EventWriter
{
    public const EXPECTED_VERSION = 'ES-ExpectedVersion';
    public const CURRENT_VERSION = 'ES-CurrentVersion';

    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @var RequestFactory
     */
    private $requestFactory;

    /**
     * @var MessageSerializer
     */
    private $serializer;

    /**
     * @param HttpClient      $client
     * @param RequestFactory  $requestFactory
     * @param MessageSerializer $serializer
     */
    public function __construct(
        HttpClient $client,
        RequestFactory $requestFactory,
        MessageSerializer $serializer
    ) {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->serializer = $serializer;
    }

    /**
     * @param Message $message
     * @param string  $streamName
     *
     * @throws Exception
     */
    public function __invoke(Message $message, string $streamName): void
    {
        $serialized = $this->serializer->serializeMessage($message);

        $currentVersion = $message->header(Header::AGGREGATE_ROOT_VERSION);
        $expectedVersion = $currentVersion ? $currentVersion -2  : -1;

        $request = $this
            ->requestFactory
            ->writeEvents(
                $streamName,
                [
                    [
                        'eventId' => (string) Uuid::uuid4(),
                        'eventType' => $serialized['headers'][Header::EVENT_TYPE],
                        'data' => $serialized['payload'],
                        'metadata' => $serialized['headers'],
                    ]
                ]
            )
            ->withHeader(self::EXPECTED_VERSION, (string) $expectedVersion)
        ;

        $response = $this->client->sendRequest($request);
        if ($response->getStatusCode() === 400) {
            throw ConcurrencyException::withVersions(
                $expectedVersion,
                (int) $response->getHeaderLine(self::CURRENT_VERSION)
            );
        }
        if ($response->getStatusCode() > 400) {
            throw new RuntimeException(
                sprintf('Unexpected response from eventstore: %d', $response->getStatusCode())
            );
        }
    }
}
