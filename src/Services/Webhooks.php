<?php

declare(strict_types=1);

namespace Mailifica\Services;

use Mailifica\Exceptions\InvalidSignatureException;

class Webhooks
{
    /**
     * Verify HMAC SHA-256 webhook signature.
     */
    public static function verifySignature(string $payload, string $signature, string $secret): bool
    {
        if (empty($payload) || empty($signature) || empty($secret)) {
            return false;
        }

        $computedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($computedSignature, $signature);
    }

    /**
     * Verify signature and construct event array.
     *
     * @throws InvalidSignatureException
     */
    public static function constructEvent(string $payload, string $signature, string $secret): array
    {
        if (!self::verifySignature($payload, $signature, $secret)) {
            throw new InvalidSignatureException();
        }

        $event = json_decode($payload, true);
        if (!is_array($event)) {
            throw new InvalidSignatureException("Invalid webhook JSON payload.");
        }

        return $event;
    }
}
