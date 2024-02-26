<?php

namespace App\Exceptions;

class InvalidBankException extends \RuntimeException
{
    public function __construct(string $bank) {
        parent::__construct(sprintf("Bank %s doesnt exist on Bank Enumeration", $bank));
    }
}
