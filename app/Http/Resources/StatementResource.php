<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StatementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'transaction' => $this->id,
            'amount' => $this->amount,
            'date' => $this->created_at,
            'is_incoming' => $this->payee_wallet_id == $request->wallet->id
        ];
    }
}
