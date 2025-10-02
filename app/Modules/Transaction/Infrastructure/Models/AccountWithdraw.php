<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Infrastructure\Models;

use Hyperf\Database\Model\Concerns\HasUuids;
use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\DbConnection\Model\Model;

/**
 */
class AccountWithdraw extends Model
{
    use HasUuids;
    
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'accounts_withdraws';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'account_id',
        'method',
        'amount',
        'scheduled',
        'scheduled_for',
        'status',
        'done',
        'error',
        'error_reason',
        'done_at',
        'denied_at',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'amount' => 'float',
    ];

    public function pix(): HasOne
    {
        return $this->hasOne(AccountWithdrawPix::class, 'account_withdraw_id');
    }
}
