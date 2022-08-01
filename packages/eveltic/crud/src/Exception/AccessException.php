<?php

namespace Eveltic\Crud\Exception;


use Throwable;

class AccessException extends \Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}