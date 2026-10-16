<?php

declare(strict_types=1);

namespace Lumi\Exceptions;

use Exception;

class LumiException extends Exception
{
    private int $statusCode;
    private array $details;

    public function __construct(string $message = "", int $statusCode = 0, array $details = [], ?Exception $previous = null)
    {
        parent::__construct($message, $statusCode, $previous);
        $this->statusCode = $statusCode;
        $this->details = $details;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getDetails(): array
    {
        return $this->details;
    }
}
