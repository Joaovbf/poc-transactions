<?php


namespace App\Services;


use App\Models\DTO\OutsideTransferDTO;
use App\Models\Wallet;
use App\Repositories\TransactionRepository;
use App\Services\Bank\SendingInvalidTransferException;
use App\Services\Interfaces\BankApi;

class TransactionService
{
    public function __construct(
        private TransactionRepository $repository,
        private BankApi $bankApi
    ) {
    }

    /**
     * @return bool
     */
    public function makeTransfer(array $transferData) {
        $payerWallet = Wallet::where('user_id',$transferData["payer_id"])->first();
        $payeeWallet = Wallet::where('user_id',$transferData["payee_id"])->first();

        if ($payerWallet->amount < $transferData["amount"]) {
            return false;
        }

        return $this->repository->effectuateTransaction($transferData['amount'], $payerWallet, $payeeWallet);
    }

    /**
     * @throws SendingInvalidTransferException
     */
    public function makeOutsideTransfer(OutsideTransferDTO $DTO) {
        $payerWallet = Wallet::where('user_id', $DTO->payer_id)->first();

        if ($payerWallet->amount < $DTO->amount) {
            return false;
        }

        $this->bankApi->sendTransfer($DTO->payee_document, $DTO->amount);

        return $this->repository->effectuateTransaction($DTO->amount, $payerWallet);
    }
}
