<?php

namespace App\Services\Interfaces;

interface BankApi
{
    public function sendTransfer(string $payeeDocument, float $amount): void;
}
