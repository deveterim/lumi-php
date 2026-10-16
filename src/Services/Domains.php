<?php

declare(strict_types=1);

namespace Lumi\Services;

use Lumi\Client;

class Domains
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Create a new domain.
     */
    public function create(array $payload): array
    {
        return $this->client->post('domains', $payload);
    }

    /**
     * Retrieve a domain by ID.
     */
    public function get(string $domainId): array
    {
        return $this->client->get('domains/' . $domainId);
    }

    /**
     * List all registered domains.
     */
    public function list(): array
    {
        return $this->client->get('domains');
    }

    /**
     * Trigger DNS verification for a domain.
     */
    public function verify(string $domainId): array
    {
        return $this->client->post('domains/' . $domainId . '/verify');
    }

    /**
     * Delete a domain.
     */
    public function delete(string $domainId): array
    {
        return $this->client->delete('domains/' . $domainId);
    }
}
