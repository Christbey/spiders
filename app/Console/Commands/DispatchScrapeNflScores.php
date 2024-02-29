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
        $types = ['reg', 'pre', 'post'];
        $wks = range(3, 3);
        $years = range(1970, 1970);

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
                }
            }
        }
    }

}
