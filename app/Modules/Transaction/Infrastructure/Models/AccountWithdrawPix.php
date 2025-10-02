<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Infrastructure\Models;

use Hyperf\Database\Model\Concerns\HasUuids;
use Hyperf\DbConnection\Model\Model;

/**
 */
class AccountWithdrawPix extends Model
{
    use HasUuids;
    
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'accounts_withdraws_pix';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'account_withdraw_id',
        'type',
        'key',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
    ];
}
