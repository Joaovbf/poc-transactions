<?php

namespace App\Services\Bank;

use App\Services\Interfaces\BankApi;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class DummyBankApi implements BankApi
{
    public function __construct(
        private Client $client,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @throws SendingInvalidTransferException|\GuzzleHttp\Exception\GuzzleException
     */
    public function sendTransfer(string $payeeDocument, float $amount): void {
        try {
            return;
            $this->client->request("POST", "api/enviar/valor", [
                'json' => [
                    'document' => $payeeDocument,
                    'amount' => $amount
                ]
            ]);
        } catch (ClientException $exception) {
            $response = json_decode($exception->getResponse()->getBody()->getContents());
            if ($response['erro'] == "recebedor não encontrado") {
                $type = InvalidTransferType::PAYEE_NOT_FOUND;
            } else {
                $type = InvalidTransferType::PAYEE_DOCUMENT_MALFORMED;
            }

            throw new SendingInvalidTransferException($type, $response['erro'], $exception);
        }
    }
}
