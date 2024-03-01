<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ScrapeNflScores; // Make sure to import the job class

class DispatchScrapeNflScores extends Command
{
    protected $signature = 'dispatch:scrape-nfl-scores';
    protected $description = 'Dispatches the ScrapeNflScores job for specified configurations.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $types = ['reg'];
        $wks = range(1, 16);
        $years = range(1979, 1999);

        foreach ($years as $year) {
            foreach ($types as $type) {
                foreach ($wks as $wk) {
                    $configuration = [
                        'year' => $year,
                        'type' => $type,
                        'wk' => $wk,
                    ];
                    // Correctly dispatch the job
                    ScrapeNflScores::dispatch($configuration);
                    $this->info("Dispatched ScrapeNflScores job for year: {$year}, type: {$type}, wk: {$wk}.");
                    sleep(5);

                }
            }
        }
    }



}
