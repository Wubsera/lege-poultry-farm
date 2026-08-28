<?php

namespace App\Support;

class MoneyFormatter
{
    /**
     * Format an amount as Ethiopian Birr.
     *
     * Example:
     * 1234.5 => 1,234.50 ETB
     */
    public static function format(
        float|int|string|null $amount
    ): string {
        return number_format(
            (float) ($amount ?? 0),
            2,
            '.',
            ','
        ) . ' ETB';
    }
}
