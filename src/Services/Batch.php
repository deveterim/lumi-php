<?php

declare(strict_types=1);

namespace Lumi\Services;

use Lumi\Client;

class Batch
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Send batch emails.
     *
     * @param array<array{
     *     from: string,
     *     to: string|array<string>,
     *     subject: string,
     *     html?: string,
     *     text?: string
     * }> $emails
     * @return array
     */
    public function send(array $emails): array
    {
        return $this->client->post('emails/batch', ['emails' => $emails]);
    }
}
