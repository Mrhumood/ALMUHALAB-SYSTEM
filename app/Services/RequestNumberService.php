<?php

namespace App\Services;

use App\Models\RequestSequence;
use Illuminate\Support\Facades\DB;

class RequestNumberService
{
    /**
     * Generate the next request number atomically.
     * Example output: SR-2026-00001
     */
    public static function generate(string $prefix = 'SR'): string
    {
        return DB::transaction(function () use ($prefix) {
            $year = now()->year;

            $seq = RequestSequence::lockForUpdate()
                ->firstOrCreate(
                    ['prefix' => $prefix, 'year' => $year],
                    ['last_number' => 0]
                );

            $seq->increment('last_number');
            $seq->refresh();

            return sprintf('%s-%d-%05d', $prefix, $year, $seq->last_number);
        });
    }

    /**
     * Returns the next simple display number (1, 2, 3...).
     * Uses prefix='NUM', year=0 in request_sequences — global and never resets.
     */
    public static function nextDisplayNumber(): int
    {
        return DB::transaction(function () {
            $seq = RequestSequence::lockForUpdate()
                ->firstOrCreate(
                    ['prefix' => 'NUM', 'year' => 0],
                    ['last_number' => 0]
                );

            $seq->increment('last_number');
            $seq->refresh();

            return $seq->last_number;
        });
    }
}
