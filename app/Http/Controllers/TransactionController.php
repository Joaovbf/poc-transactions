<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidBankException;
use App\Http\Requests\OutsideTransactionRequest;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\StatementResource;
use App\Models\DTO\OutsideTransferDTO;
use App\Models\Wallet;
use App\Repositories\TransactionRepository;
use App\Services\Bank\SendingInvalidTransferException;
use App\Services\TransactionService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;
use Psr\Log\LoggerInterface;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService,
        private TransactionRepository $transactionRepository,
        private LoggerInterface $logger
    ) {
    }

    public function transferToOtherBank(OutsideTransactionRequest $request) {
        try {
            $dto = new OutsideTransferDTO(...$request->validated());
            $this->transactionService->makeOutsideTransfer($dto);
        } catch (InvalidBankException $exception) {
            $errorCode = Response::HTTP_BAD_REQUEST;
            return $this->errorResponse($exception, $errorCode);
        } catch (SendingInvalidTransferException $exception) {
            return $this->errorResponse($exception, Response::HTTP_BAD_REQUEST);
        } catch (Exception $exception) {
            return $this->errorResponse($exception);
        }

        return response('ok');
    }

    public function transfer(TransactionRequest $request) {
        $isSuccess = $this->transactionService->makeTransfer($request->all());
        $returnCode = $isSuccess ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR;
        return response('ok', $returnCode);
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

        $transactions = $this->transactionRepository->getAllFromPeriod($wallet->id, $data['period_start'], $data['period_end']);
        return StatementResource::collection($transactions)
            ->additional(['current_amount' => $wallet->amount]);
    }

    private function errorResponse(\Throwable $exception, int $errorCode = Response::HTTP_INTERNAL_SERVER_ERROR) {
        $this->logger->error($exception->getMessage(), $exception->getTrace());
        return response($exception->getMessage(), $errorCode);
    }
}
