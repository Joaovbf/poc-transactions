<?php

namespace App\Models\DTO;

use App\Exceptions\InvalidBankException;
use App\Models\Enum\Bank;

class OutsideTransferDTO
{
    public function __construct(
        public readonly float $amount,
        public readonly int $payer_id,
        public readonly string $payee_document,
        private string $bank
    ) {
        if (null === Bank::tryFrom($this->bank)) {
            throw new InvalidBankException($this->bank);
        }
    }

    public function getBank(): Bank
    {
        return Bank::tryFrom($this->bank);
    }
}
