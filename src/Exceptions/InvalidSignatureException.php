<?php

declare(strict_types=1);

namespace Mailifica\Exceptions;

class InvalidSignatureException extends MailificaException
{
    public function __construct(string $message = "The provided webhook signature is invalid or does not match the payload.")
    {
        parent::__construct($message, 400);
    }
}
