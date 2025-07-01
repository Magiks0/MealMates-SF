<?php

namespace App\Enum;

enum PurchaseStatus: string
{
    case PENDING = 'awaiting_pickup';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
  
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
