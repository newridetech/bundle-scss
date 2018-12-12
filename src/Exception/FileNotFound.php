<?php

namespace Newride\Scss\Exception;

use Newride\Scss\Exception;
use Throwable;

class FileNotFound extends Exception
{
    public function __construct(string $filename, int $code = 0, Throwable $throwable = null)
    {
        parent::__construct(
            sprintf('Scss stylesheet does not exist: %s', $filename),
            $code,
            $throwable
        );
    }
}
