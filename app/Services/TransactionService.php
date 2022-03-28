<?php


namespace App\Services;


use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Repositories\TransactionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionService
{
    /**
     * @return bool
     */
    public static function makeTransfer(array $transferData) {
        $payerWallet = Wallet::where('user_id',$transferData["payer_id"])->first();
        $payeeWallet = Wallet::where('user_id',$transferData["payee_id"])->first();

        if ($payerWallet->amount < $transferData["amount"]){
            return false;
        }

        return TransactionRepository::effectuateTransaction($transferData['amount'],$payeeWallet,$payerWallet);
    }
}
