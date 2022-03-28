<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Http\Resources\StatementResource;
use App\Models\Wallet;
use App\Repositories\TransactionRepository;
use App\Services\TransactionService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function transfer(TransactionRequest $request) {
        $isSuccess = TransactionService::makeTransfer($request->all());
        $returnCode = $isSuccess ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR;
        return response('', $returnCode);
    }

    /**
     * @param Wallet $wallet
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getStatement(Wallet $wallet) {
        $data = request(['period_start','period_end']);
        Validator::make($data, [
            'period_start' => 'required|date|before:today',
            'period_end' => 'required|date|after:period_start'
        ])->validate();

        $transactions = TransactionRepository::getAllFromPeriod($wallet->id, $data['period_start'], $data['period_end']);
        return StatementResource::collection($transactions)
            ->additional(['current_amount' => $wallet->amount]);
    }
}
