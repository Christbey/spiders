<?php

namespace App\Jobs;

use App\Events\ConfigurationCompleted;
use App\Models\NflGame;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Symfony\Component\DomCrawler\Crawler;

class ScrapeNflScores implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $configuration;

    public function __construct($configuration)
    {
        $this->configuration = $configuration;
    }

    public function handle()
    {
        \Log::info('Starting to scrape NFL scores with configuration:', $this->configuration);
        event(new ConfigurationCompleted($this->configuration));

        $teamMap = $this->getTeamMap();
        $client = new Client();
        $headers = $this->getRequestHeaders();
        $configuration = $this->configuration;

        try {
            $url = $this->buildUrl($configuration);
            $response = $client->request('GET', $url, ['headers' => $headers]);
            $html = (string)$response->getBody();
            $crawler = new Crawler($html);

            // Delegate the scraping logic to a dedicated method
            $this->scrapeGames($crawler, $teamMap, $configuration['year'], $configuration['type'], $configuration['wk']);

            event(new ConfigurationCompleted($configuration));
            \Log::info('Scraping completed successfully.', ['configuration' => $this->configuration]);

        } catch (\Exception $e) {
            // Logging the error to easily track issues through log files
            \Log::error("An error occurred while scraping NFL scores: {$e->getMessage()}", ['configuration' => $this->configuration]);

            // Consider re-throwing the exception to let Laravel's exception handler deal with it
            // or handling it as needed based on your application's requirements
            throw $e;
        }
    }

    private function getTeamMap(): array
    {
        return DB::table('nfl_teams')->select('id', 'api_name')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->api_name => $item->id];
            })->all();
    }

    private function buildUrl(array $configuration): string
    {
        $queryParams = http_build_query($configuration);
        return 'https://www.footballdb.com/scores/index.html?' . $queryParams;
    }

    private function scrapeGames(Crawler $crawler, array $teamMap, string $year, string $type, string $week): void
    {
        $crawler->filter('.section_half .lngame')->each(function (Crawler $gameNode) use ($teamMap, $year, $type, $week) {
            $gameDateNode = $gameNode->filter('th.left');
            $gameDateText = $gameDateNode->text();
            $parsedDate = $this->parseGameDate($gameDateText);

            $gameNode->filter('table.fb_component_tbl tbody')->each(function (Crawler $tableBody) use ($teamMap, $year, $type, $week, $parsedDate) {
                $rows = $tableBody->children('tr');
                if ($rows->count() >= 2) {
                    $visitorTeamDetails = $this->extractTeamDetails($rows->eq(0));
                    $homeTeamDetails = $this->extractTeamDetails($rows->eq(1));

                    // Check if team details are known before proceeding
                    if ($visitorTeamDetails['name'] !== 'Unknown' && $homeTeamDetails['name'] !== 'Unknown') {
                        $visitorTeamId = $teamMap[$visitorTeamDetails['name']] ?? null;
                        $homeTeamId = $teamMap[$homeTeamDetails['name']] ?? null;

                        // Only proceed if both team IDs are found
                        if ($visitorTeamId && $homeTeamId) {
                            $gameId = $year. substr($parsedDate['game_date'],4) . substr($visitorTeamDetails['name'], 0, 2) . substr($homeTeamDetails['name'], 0, 2);

                            NflGame::updateOrCreate(
                                ['game_id' => $gameId],
                                [
                                    'team_away_record' => $visitorTeamDetails['record'],
                                    'team_home_record' => $homeTeamDetails['record'],
                                    'year' => $year,
                                    'type' => $type,
                                    'week' => $week,
                                    'team_away_id' => $teamMap[$visitorTeamDetails['name']] ?? null,
                                    'score_away' => $visitorTeamDetails['score'],
                                    'team_home_id' => $teamMap[$homeTeamDetails['name']] ?? null,
                                    'score_home' => $homeTeamDetails['score'],
                                    'team_home_name' => $homeTeamDetails['name'],
                                    'team_away_name' => $visitorTeamDetails['name'],
                                    'game_date' =>  $year. substr($parsedDate['game_date'],4),
                                    'game_day' => $parsedDate['game_day'],
                                ]
                            );
                        } else {
                            \Log::warning('One or both team IDs not found', ['visitorTeam' => $visitorTeamDetails['name'], 'homeTeam' => $homeTeamDetails['name']]);
                        }
                    } else {
                        \Log::info('Skipping record due to unknown team details', [
                            'visitorTeamDetails' => $visitorTeamDetails,
                            'homeTeamDetails' => $homeTeamDetails,
                        ]);
                    }
                }
            });
        });
    }

    private function parseGameDate(string $gameDateText): array
    {
        try {
            $date = new \DateTime($gameDateText);
        } catch (\Exception $e) {
            $this->error('Failed to parse game date: ' . $e->getMessage());
            // Return empty values in case of an error
            return ['game_date' => '', 'game_day' => ''];
        }

        // Format the date as 'YYYYMMDD', e.g., '20230901'
        $formattedDate = $date->format('Ymd');

        // Get the day of the week, e.g., 'Monday'
        $dayOfWeek = $date->format('l');

        // Return both pieces of information in an associative array
        return [
            'game_date' => $formattedDate,
            'game_day' => $dayOfWeek,
        ];
    }

    private function extractTeamDetails(Crawler $rowNode): array
    {
        $teamNameText = $rowNode->filter('td.left')->text();
        $score = $rowNode->filter('td.center')->last()->text();

        preg_match('/^(.*?)\s+\((\d+-\d+)\)$/', $teamNameText, $matches);
        $name = $matches[1] ?? 'Unknown';
        $record = $matches[2] ?? 'Unknown';

        // Add logging to check extracted values
        \Log::info('Extracted team details', ['name' => $name, 'record' => $record]);

        return [
            'name' => $name,
            'record' => $record,
            'score' => $score,
        ];
    }

        private function getRequestHeaders(): array
    {
        return [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language' => 'en-US,en;q=0.5',
  
        ];

    }
}

