<?php

declare(strict_types=1);

namespace Lumi\Exceptions;

class ApiKeyMissingException extends LumiException
{
    public function __construct(string $message = "Mailifica API key is missing. Pass it to the constructor or set LUMI_API_KEY environment variable.")
    {
        parent::__construct($message, 401);
    }
}
