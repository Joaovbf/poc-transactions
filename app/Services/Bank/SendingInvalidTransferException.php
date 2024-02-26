<?php

namespace App\Services\Bank;

class SendingInvalidTransferException extends \Exception
{
    public function __construct(
        public readonly InvalidTransferType $transferType,
        string $message = "",
        \Throwable $previous = null
    ) {
        parent::__construct(
            message: $message,
            previous: $previous
        );
    }
}
