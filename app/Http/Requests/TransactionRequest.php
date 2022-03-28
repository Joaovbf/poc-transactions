<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'amount' => 'numeric|min:0',
            'payer_id' => [
                Rule::exists('users',"id")->where(function ($query){
                    return $query->where('type', 'person');
                })
            ],
            'payee_id' => 'numeric|exists:users,id|different:payer_id'
        ];
    }
}
