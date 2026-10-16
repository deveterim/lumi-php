<?php

declare(strict_types=1);

namespace Mailifica\Exceptions;

class ApiKeyMissingException extends MailificaException
{
    public function __construct(string $message = "Mailifica API key is missing. Pass it to the constructor or set MAILIFICA_API_KEY environment variable.")
    {
        parent::__construct($message, 401);
    }
}
