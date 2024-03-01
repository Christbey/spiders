<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\TournamentTeam; // Make sure to import your model

class ScrapeNCAABracketology extends Command
{
    protected $signature = 'scrape:bracket';
    protected $description = 'Scrapes NCAA bracket projections for 2024 and stores in the database.';

    public function handle()
    {
        $url = 'https://www.teamrankings.com/ncaa-tournament/bracket-predictions/';
        $client = new Client();
        $response = $client->request('GET', $url);
        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html);

        $crawler->filter('table.datatable tbody tr')->each(function (Crawler $row) {
            // Check if 'seed' is null
            if ($row->filter('td:nth-child(1)')->text() === '') {
                return false; // Stop if 'seed' is null
            }

            $seed = $row->filter('td:nth-child(1)')->text();
            $teamName = $row->filter('td:nth-child(3) .table-team-logo-text a')->text();

            // Insert or update the team in the database
            TournamentTeam::updateOrCreate(
                ['name' => $teamName], // Assuming 'name' is a unique identifier
                ['seed' => intval($seed), 'name' => $teamName]
            );
        });

        // Feedback to the user
        $this->info('Teams and seeds scraped and stored in the database.');
    }
}
