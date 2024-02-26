<?php
/**
 * Created by PhpStorm.
 * User: jvito
 * Date: 26/03/2022
 * Time: 15:04
 */

namespace App\Repositories;


use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionRepository
{
    /**
     * @param $wallet_id
     * @param $periodStart
     * @param $periodEnd
     *
     * @return Collection
     */
    public function getAllFromPeriod($wallet_id, $periodStart, $periodEnd) {
        return Transaction::where(
                function ($query) use ($wallet_id){
                    $query->where('payee_wallet_id',$wallet_id)->orWhere('payer_wallet_id',$wallet_id);
                }
            )->whereBetween('created_at',[$periodStart,$periodEnd])->get();
    }

    public function effectuateTransaction(float $amount, Wallet $payerWallet, Wallet $payeeWallet = null): bool {
        try {
            DB::beginTransaction();
            $transactionData = [
                'amount' => $amount,
                'payer_wallet_id' => $payerWallet->id
            ];

            if ($payeeWallet !== null) {
                $transactionData['payee_wallet_id'] = $payeeWallet->id;
                $payeeWallet->amount += $amount;
                $payeeWallet->save();
            }

            Transaction::create($transactionData);

            $payerWallet->amount -= $amount;
            $payerWallet->save();

            DB::commit();
            return true;
        } catch (\Exception $exception) {
            Log::error($exception);
            DB::rollBack();
            return false;
        }
    }
}
