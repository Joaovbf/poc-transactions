<?php
/**
 * Created by PhpStorm.
 * User: jvito
 * Date: 26/03/2022
 * Time: 15:04
 */

namespace App\Repositories;


use App\Models\Transaction;
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
    public static function getAllFromPeriod($wallet_id, $periodStart, $periodEnd) {
        return Transaction::where(
                function ($query) use ($wallet_id){
                    $query->where('payee_wallet_id',$wallet_id)->orWhere('payer_wallet_id',$wallet_id);
                }
            )->whereBetween('created_at',[$periodStart,$periodEnd])->get();
    }

    /**
     * @param $amount
     * @param $payeeWallet
     * @param $payerWallet
     *
     * @return bool
     */
    public static function effectuateTransaction($amount, $payeeWallet, $payerWallet) {
        try {
            DB::beginTransaction();
            Transaction::create([
                'amount' => $amount,
                'payee_wallet_id' => $payeeWallet->id,
                'payer_wallet_id' => $payerWallet->id
            ]);

            $payerWallet->amount -= $amount;
            $payeeWallet->amount += $amount;
            $payeeWallet->save();
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
