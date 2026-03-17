<?php

namespace App\Console\Commands;

use App\Models\Course;
use Illuminate\Console\Command;
use Laravel\Scout\EngineManager;
use Meilisearch\Client as MeilisearchClient;

class MeilisearchSetup extends Command
{
    protected $signature   = 'meilisearch:setup {--reimport : Also reimport all published courses after configuring}';
    protected $description = 'Configure Meilisearch index settings for LearnFlow';

    public function handle(EngineManager $manager): int
    {
        /** @var MeilisearchClient $client */
        $client = $manager->engine('meilisearch')->meilisearch();

        $indexName = (new Course)->searchableAs();
        $index     = $client->index($indexName);

        $this->info("Configuring index: {$indexName}");

        $index->updateSearchableAttributes(['title', 'short_description', 'instructor_name']);
        $this->line('  ✓ searchableAttributes');

        $index->updateFilterableAttributes(['level', 'language', 'price', 'status']);
        $this->line('  ✓ filterableAttributes');

        $index->updateSortableAttributes(['enrolled_count', 'average_rating', 'price', 'created_at']);
        $this->line('  ✓ sortableAttributes');

        $index->updateRankingRules([
            'words', 'typo', 'proximity', 'attribute', 'sort', 'exactness',
        ]);
        $this->line('  ✓ rankingRules');

        $index->updateTypoTolerance([
            'enabled' => true,
            'minWordSizeForTypos' => ['oneTypo' => 4, 'twoTypos' => 8],
        ]);
        $this->line('  ✓ typoTolerance');

        if ($this->option('reimport')) {
            $this->info('Reimporting published courses…');
            $this->call('scout:import', ['model' => Course::class]);
        }

        $this->info('Meilisearch setup complete.');

        return self::SUCCESS;
    }
}
