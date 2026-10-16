<?php

declare(strict_types=1);

namespace Lumi;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Lumi\Exceptions\ApiKeyMissingException;
use Lumi\Exceptions\LumiException;

class Client
{
    public const VERSION = '1.0.2';
    public const DEFAULT_BASE_URL = 'https://api.lumi.email/v1';

    private string $apiKey;
    private string $baseUrl;
    private ClientInterface $httpClient;

    public function __construct(?string $apiKey = null, ?string $baseUrl = null, ?ClientInterface $httpClient = null)
    {
        $resolvedKey = $apiKey ?? getenv('LUMI_API_KEY') ?: '';
        if (empty($resolvedKey)) {
            throw new ApiKeyMissingException();
        }

        $this->apiKey = $resolvedKey;
        $this->baseUrl = rtrim($baseUrl ?? getenv('LUMI_BASE_URL') ?: self::DEFAULT_BASE_URL, '/');
        $this->httpClient = $httpClient ?? new GuzzleClient([
            'timeout' => 30.0,
            'http_errors' => false,
        ]);
    }

    public function request(string $method, string $path, array $options = []): array
    {
        $url = $this->baseUrl . '/' . ltrim($path, '/');

        $headers = array_merge([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' => 'lumi-php/' . self::VERSION,
        ], $options['headers'] ?? []);

        $requestOptions = [
            'headers' => $headers,
        ];

        if (isset($options['json'])) {
            $requestOptions['json'] = $options['json'];
        }

        if (isset($options['query'])) {
            $requestOptions['query'] = $options['query'];
        }

        try {
            $response = $this->httpClient->request($method, $url, $requestOptions);
            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();

            $data = json_decode($body, true) ?? [];

            if ($statusCode >= 400) {
                $message = $data['message']
                    ?? (is_array($data['error'] ?? null) ? ($data['error']['message'] ?? json_encode($data['error'])) : ($data['error'] ?? 'HTTP request failed with status code ' . $statusCode));
                throw new LumiException($message, $statusCode, $data);
            }

            return $data;
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $statusCode = $response ? $response->getStatusCode() : 0;
            $body = $response ? (string) $response->getBody() : '';
            $data = json_decode($body, true) ?? [];
            $message = $data['message']
                ?? (is_array($data['error'] ?? null) ? ($data['error']['message'] ?? json_encode($data['error'])) : ($data['error'] ?? $e->getMessage()));

            throw new LumiException($message, $statusCode, $data, $e);
        } catch (GuzzleException $e) {
            throw new LumiException($e->getMessage(), (int) $e->getCode(), [], $e);
        }
    }

    public function get(string $path, array $query = []): array
    {
        return $this->request('GET', $path, ['query' => $query]);
    }

    public function post(string $path, array $data = []): array
    {
        return $this->request('POST', $path, ['json' => $data]);
    }

    public function patch(string $path, array $data = []): array
    {
        return $this->request('PATCH', $path, ['json' => $data]);
    }

    public function delete(string $path): array
    {
        return $this->request('DELETE', $path);
    }
}
