<?php

declare(strict_types=1);

namespace App\Modules\Account\Infrastructure\Models;

use Hyperf\Database\Model\Concerns\HasUuids;
use Hyperf\DbConnection\Model\Model;

/**
 */
class Account extends Model
{
    use HasUuids;
    
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'accounts';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'name',
        'balance',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'balance' => 'float',
    ];
}
