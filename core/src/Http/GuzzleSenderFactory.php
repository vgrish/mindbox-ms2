<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Http;

use GuzzleHttp\BodySummarizer;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class GuzzleSenderFactory implements RequestSenderFactoryInterface
{
    /**
     * Стандартная фабрика Guzzle.
     *
     * @param int   $retries             количество повторных попыток отправки запроса в случае неудачи
     * @param int   $exceptionTruncateAt максимальный размер сообщения об ошибке
     * @param array $requestOptions      параметры запросов по умолчанию (https://docs.guzzlephp.org/en/stable/request-options.html)
     */
    public function __construct(
        private readonly int $retries = 0,
        private readonly int $exceptionTruncateAt = 4000,
        private readonly array $requestOptions = [],
    ) {
    }

    public function make(): GuzzleSender
    {
        return $this->makeFromHandler(null);
    }

    protected function makeFromHandler(?callable $handler): GuzzleSender
    {
        $handlerStack = HandlerStack::create($handler);
        $client = $this->createClient($handlerStack);

        return new GuzzleSender($client, $this->requestOptions);
    }

    protected function createClient(HandlerStack $handlerStack): Client
    {
        $this->pushMiddlewares($handlerStack);

        return new Client(['handler' => $handlerStack]);
    }

    protected function pushMiddlewares(HandlerStack $handlerStack): void
    {
        $handlerStack->push(Middleware::retry(
            $this->getDecider(),
            $this->getDelay(),
        ));
        $handlerStack->push(
            Middleware::httpErrors(new BodySummarizer($this->exceptionTruncateAt)),
            'http_errors',
        );
    }

    protected function getDecider(): callable
    {
        return function (
            int $currentRetry,
            RequestInterface $request,
            ?ResponseInterface $response,
            ?\Throwable $exception,
        ) {
            if ($currentRetry >= $this->retries) {
                return false;
            }

            if (!$exception instanceof GuzzleException) {
                return false;
            }

            if ($exception instanceof ConnectException) {
                return true;
            }

            $statusCode = $response?->getStatusCode() ?? $exception?->getCode();

            return 500 <= $statusCode || 429 === $statusCode;
        };
    }

    protected function getDelay(): ?callable
    {
        return null;
    }
}
