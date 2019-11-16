<?php
declare(strict_types=1);


namespace Lukasz93P\objectSerializer\exceptions;


use RuntimeException;
use Throwable;

abstract class DeserializationFailed extends RuntimeException
{
    protected function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}