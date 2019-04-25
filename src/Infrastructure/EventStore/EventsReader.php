<?php

declare(strict_types=1);

namespace Infrastructure\EventStore;

use EventSauce\EventSourcing\Serialization\MessageSerializer;
use Generator;
use Http\Client\Exception;
use Http\Client\HttpClient;
use Psr\Http\Message\RequestInterface;
use RuntimeException;

final class EventsReader
{
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
     * @param string $streamName
     *
     * @return Generator
     * @throws Exception
     */
    public function __invoke(string $streamName): Generator
    {
        $request = $this->requestFactory->startReadingEvents(
            $streamName
        );

        foreach ($this->fetchAllEvents($request, $streamName) as $event) {
            yield from $this->serializer->unserializePayload([
                'headers' => json_decode($event['metaData'], true),
                'payload' => json_decode($event['data'], true),
            ]);
        }
    }

    /**
     * @param RequestInterface $request
     * @param string           $streamName
     *
     * @return array
     * @throws Exception
     */
    private function fetchAllEvents(RequestInterface $request, string $streamName): array
    {
        $response = $this->client->sendRequest(
            $request
        );

        // Stream not found.
        if ($response->getStatusCode() === 404) {
            return [];
        }

        if ($response->getStatusCode() >= 300) {
            throw new RuntimeException(
                sprintf('Unexpected response from eventstore: %d', $response->getStatusCode())
            );
        }
        $data = json_decode((string) $response->getBody(), true);
        $entries = array_reverse($data['entries']);

        foreach ($data['links'] as $link) {
            if ($link['relation'] === RequestFactory::DIRECTION_FORWARD) {
                $request = $this->requestFactory->readEvents($link['uri']);

                $entries = array_merge(
                    $entries,
                    $this->fetchAllEvents($request, $streamName)
                );
            }
        }

        return $entries;
    }
}
