<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

namespace Vgrish\MindBox\MS2\Http;

use GuzzleHttp\Psr7\Request;
use Vgrish\MindBox\MS2\Tools\Url;

class ApiClient
{
    private array $headers = [
        'Content-Type' => 'application/json',
        'X-Customer-IP' => '',
    ];

    public function __construct(
        array $credentials,
        private readonly RequestSenderInterface $requestSender,
    ) {
        $this->addCredentialsToHeaders($credentials);
    }

    /**
     * @throws \Exception
     */
    public function send(Payload $payload): array
    {
        $content = $this->sendRequest($payload);

        if ('' === $content) {
            return [];
        }

        try {
            $encodedContent = \json_decode($content, true, 512, \JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            $encodedContent = ['status' => 'Error', 'errorMessage' => $e->getMessage()];
        }

        return $encodedContent;
    }

    public function debug(Payload $payload)
    {
        $url = Url::make($payload);

        return [
            'method' => $payload->method->value,
            'url' => \urldecode($url),
            'url_encoded' => $url,
            'headers' => $this->headers,
            'body' => $payload->body,
        ];
    }

    public function addParamsToHeaders(array $params): void
    {
        foreach ($params as $key => $credential) {
            $this->headers[$key] = $credential;
        }
    }

    /**
     * @throws \Exception
     */
    private function sendRequest(Payload $payload): string
    {
        $uri = Url::make($payload);
        $body = $payload->body;
        $request = new Request($payload->method->value, $uri, $this->headers, $body);

        try {
            return $this->requestSender
                ->send($request)
                ->getBody()
                ->getContents();
        } catch (\Throwable $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function addCredentialsToHeaders(array $credentials): void
    {
        if (empty($credentials) || \array_is_list($credentials)) {
            throw new \InvalidArgumentException('Credentials should not be a list array');
        }

        if ($secretKey = $credentials['SecretKey'] ?? null) {
            $this->headers['Authorization'] = 'SecretKey ' . $secretKey;
            unset($credentials['SecretKey']);
        }

        if (!empty($credentials)) {
            foreach ($credentials as $key => $credential) {
                $this->headers[$key] = $credential;
            }
        }
    }
}
