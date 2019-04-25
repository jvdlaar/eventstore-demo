<?php

declare(strict_types=1);

namespace Infrastructure\EventStore;

use Nyholm\Psr7\Request;
use Nyholm\Psr7\Uri;
use Psr\Http\Message\RequestInterface;

final class RequestFactory
{
    public const DIRECTION_FORWARD = 'forward';
    public const DIRECTION_BACKWARD = 'backward';
    public const DEFAULT_COUNT = 1000;

    /**
     * @var string
     */
    private $endpoint;

    /**
     * @var array
     */
    private $defaultGetHeaders = [
        'Accept' => 'application/vnd.eventstore.atom+json',
    ];

    /**
     * @var array
     */
    private $defaultPostHeaders = [
        'Accept' => 'application/vnd.eventstore.events+json',
        'Content-Type' => 'application/json',
    ];

    /**
     * @param string $endpoint
     */
    public function __construct(string $endpoint)
    {
        $this->endpoint = rtrim($endpoint, '/');
    }

    /**
     * @param string $stream
     * @param string $direction
     * @param string $from
     * @param int    $count
     *
     * @return RequestInterface
     */
    public function startReadingEvents(
        string $stream,
        string $direction = self::DIRECTION_FORWARD,
        string $from = '0',
        int $count = self::DEFAULT_COUNT
    ): RequestInterface {
        $path = $this->createStreamUri($stream, sprintf('/%s/%s/%s', $from, $direction, $count));

        return $this->readEvents($path);
    }

    /**
     * @param string $path
     *
     * @return RequestInterface
     */
    public function readEvents(string $path): RequestInterface
    {
        $uri = (new Uri($path))
            ->withQuery('embed=body')
        ;

        return new Request('GET', $uri, $this->getHeaders());
    }

    /**
     * @param string $stream
     * @param array  $events
     *
     * @return RequestInterface
     */
    public function writeEvents(string $stream, array $events): RequestInterface
    {
        return new Request(
            'POST',
            $this->createStreamUri($stream),
            $this->postHeaders([
                'Content-Type' => 'application/vnd.eventstore.events+json',
            ]),
            json_encode($events, JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * @param string $stream
     * @param string $path
     *
     * @return string
     */
    private function createStreamUri(string $stream, string $path = '/'): string
    {
        $uri = sprintf(
            '%s/streams/%s/%s',
            $this->endpoint,
            trim($this->encodePath($stream), '/'),
            ltrim($path, '/')
        );
        return rtrim($uri, '/');
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function encodePath(string $path): string
    {
        return urlencode(str_replace('\\', '-', $path));
    }

    /**
     * @param array $headers
     *
     * @return array
     */
    private function getHeaders(array $headers = []): array
    {
        return array_replace_recursive($this->defaultGetHeaders, $headers);
    }

    /**
     * @param array $headers
     *
     * @return array
     */
    private function postHeaders(array $headers = []): array
    {
        return array_replace_recursive($this->defaultPostHeaders, $headers);
    }
}
