<?php

declare(strict_types=1);

namespace Lumi\Tests;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Lumi\Exceptions\ApiKeyMissingException;
use Lumi\Exceptions\InvalidSignatureException;
use Lumi\Exceptions\LumiException;
use Lumi\Mailifica;
use Lumi\Services\Webhooks;
use PHPUnit\Framework\TestCase;

class MailificaTest extends TestCase
{
    public function testThrowsExceptionWhenApiKeyIsMissing(): void
    {
        $this->expectException(ApiKeyMissingException::class);
        new Mailifica('');
    }

    public function testSendsEmailSuccessfully(): void
    {
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode([
                'id' => 'email_123',
                'from' => 'test@mailifica.com',
                'to' => ['recipient@example.com'],
                'status' => 'sent'
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $customHttpClient = new GuzzleClient(['handler' => $handlerStack]);

        $mailifica = new Mailifica('test_api_key', 'https://api.lumi.email/v1', $customHttpClient);

        $response = $mailifica->emails->send([
            'from' => 'test@mailifica.com',
            'to' => 'recipient@example.com',
            'subject' => 'Hello World',
            'html' => '<p>Test email</p>'
        ]);

        $this->assertEquals('email_123', $response['id']);
        $this->assertEquals('sent', $response['status']);
    }

    public function testHandlesApiErrorsCorrectly(): void
    {
        $mock = new MockHandler([
            new Response(422, ['Content-Type' => 'application/json'], json_encode([
                'error' => 'validation_error',
                'message' => 'The "from" address is invalid.'
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $customHttpClient = new GuzzleClient(['handler' => $handlerStack]);

        $mailifica = new Mailifica('test_api_key', null, $customHttpClient);

        $this->expectException(LumiException::class);
        $this->expectExceptionMessage('The "from" address is invalid.');

        $mailifica->emails->send([
            'from' => 'invalid-email',
            'to' => 'recipient@example.com',
            'subject' => 'Fail'
        ]);
    }

    public function testWebhookSignatureVerification(): void
    {
        $payload = json_encode(['event' => 'email.sent', 'data' => ['id' => 'email_123']]);
        $secret = 'whsec_test_secret';
        $signature = hash_hmac('sha256', $payload, $secret);

        $this->assertTrue(Webhooks::verifySignature($payload, $signature, $secret));
        $this->assertFalse(Webhooks::verifySignature($payload, 'invalid_sig', $secret));

        $event = Webhooks::constructEvent($payload, $signature, $secret);
        $this->assertEquals('email.sent', $event['event']);
        $this->assertEquals('email_123', $event['data']['id']);
    }

    public function testWebhookThrowsOnInvalidSignature(): void
    {
        $this->expectException(InvalidSignatureException::class);
        Webhooks::constructEvent('{"event":"test"}', 'wrong_sig', 'secret');
    }
}
