<?php

namespace App\Services\Bank;

enum InvalidTransferType
{
    case PAYEE_DOCUMENT_MALFORMED;
    case PAYEE_NOT_FOUND;
    case INSUFICIENT_FUNDS;
}
