<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount', 'user_id'
    ];

    public function receivedTransactions() {
        return $this->hasMany(Transaction::class, 'payee_wallet_id');
    }

    public function paidTransactions() {
        return $this->hasMany(Transaction::class,'payer_wallet_id');
    }
}
