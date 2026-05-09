<?php

namespace App\Console\Commands;

use App\Models\ServiceRequest;
use App\Services\RequestNumberService;
use Illuminate\Console\Command;

class BackfillRequestNumbers extends Command
{
    protected $signature   = 'requests:backfill-numbers';
    protected $description = 'Assign request numbers to existing service requests that do not have one';

    public function handle(): int
    {
        $records = ServiceRequest::withTrashed()
            ->whereNull('request_number')
            ->oldest()
            ->get();

        if ($records->isEmpty()) {
            $this->info('All requests already have a number. Nothing to do.');
            return self::SUCCESS;
        }

        $this->info("Backfilling {$records->count()} request(s)...");
        $bar = $this->output->createProgressBar($records->count());
        $bar->start();

        foreach ($records as $sr) {
            $sr->updateQuietly([
                'request_number' => RequestNumberService::generate(),
                'display_number' => RequestNumberService::nextDisplayNumber(),
            ]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done.');

        return self::SUCCESS;
    }
}
