<?php

declare(strict_types=1);

namespace Lumi\Services;

use Lumi\Client;

class Emails
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Send an email.
     *
     * @param array{
     *     from: string,
     *     to: string|array<string>,
     *     subject: string,
     *     html?: string,
     *     text?: string,
     *     cc?: string|array<string>,
     *     bcc?: string|array<string>,
     *     reply_to?: string|array<string>,
     *     headers?: array<string, string>,
     *     attachments?: array<array{filename: string, content?: string, path?: string}>,
     *     tags?: array<array{name: string, value: string}>
     * } $payload
     * @return array
     */
    public function send(array $payload): array
    {
        if (isset($payload['to']) && is_array($payload['to'])) {
            $payload['to'] = count($payload['to']) > 0 ? $payload['to'][0] : '';
        }
        if (isset($payload['reply_to']) && is_array($payload['reply_to'])) {
            $payload['reply_to'] = count($payload['reply_to']) > 0 ? $payload['reply_to'][0] : '';
        }
        return $this->client->post('emails', $payload);
    }

    /**
     * Retrieve an email by its ID.
     */
    public function get(string $emailId): array
    {
        return $this->client->get('emails/' . $emailId);
    }

    /**
     * Cancel a scheduled email.
     */
    public function cancel(string $emailId): array
    {
        return $this->client->post('emails/' . $emailId . '/cancel');
    }
}
