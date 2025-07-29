<?php

namespace Modules\Wallet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Wallet\Database\factories\WalletHistoryFactory;
use App\Models\User;

class WalletHistory extends Model
{
    use HasFactory;
    protected $table = 'wallet_history';
    protected $fillable = [
        'datetime', 'user_id', 'activity_type', 'activity_message', 'activity_data'
    ];

    protected $casts = [
        'user_id'   => 'integer',
    ];
    /**
     * The attributes that are mass assignable.
     */
    
    protected static function newFactory(): WalletHistoryFactory
    {
        
    }
    public function users(){
        return $this->belongsTo(User::class, 'user_id','id');
    }
}
