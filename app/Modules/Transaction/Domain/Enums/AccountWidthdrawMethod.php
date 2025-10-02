<?php

namespace App\Modules\Transaction\Domain\Enums;

enum AccountWidthdrawMethod: string
{
    case TED = 'TED';
    case PIX = 'PIX';
}