<?php

namespace App\Modules\Transaction\Domain\Enums;

enum WithdrawStatus: string
{
    case PENDING = 'pending';
    case DONE = 'done';
    case DENIED = 'denied';
}

