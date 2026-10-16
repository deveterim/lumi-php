<?php

declare(strict_types=1);

namespace Lumi;

use GuzzleHttp\ClientInterface;
use Lumi\Services\ApiKeys;
use Lumi\Services\Batch;
use Lumi\Services\Domains;
use Lumi\Services\Emails;
use Lumi\Services\Webhooks;

class Mailifica
{
    private Client $client;

    public Emails $emails;
    public Batch $batch;
    public Domains $domains;
    public ApiKeys $apiKeys;
    public Webhooks $webhooks;

    public function __construct(?string $apiKey = null, ?string $baseUrl = null, ?ClientInterface $httpClient = null)
    {
        $this->client = new Client($apiKey, $baseUrl, $httpClient);

        $this->emails = new Emails($this->client);
        $this->batch = new Batch($this->client);
        $this->domains = new Domains($this->client);
        $this->apiKeys = new ApiKeys($this->client);
        $this->webhooks = new Webhooks();
    }

    /**
     * Factory helper to create a new Mailifica client.
     */
    public static function client(?string $apiKey = null, ?string $baseUrl = null, ?ClientInterface $httpClient = null): self
    {
        return new self($apiKey, $baseUrl, $httpClient);
    }

    /**
     * Get the underlying HTTP client.
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}
