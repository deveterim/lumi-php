<?php

declare(strict_types=1);

namespace Mailifica\Services;

use Mailifica\Client;

class ApiKeys
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Create a new API Key.
     */
    public function create(array $payload): array
    {
        return $this->client->post('api-keys', $payload);
    }

    /**
     * List all API Keys for the account.
     */
    public function list(): array
    {
        return $this->client->get('api-keys');
    }

    /**
     * Delete / Revoke an API Key.
     */
    public function delete(string $apiKeyId): array
    {
        return $this->client->delete('api-keys/' . $apiKeyId);
    }
}
