<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Project;
use App\Proposal;
use App\PublicConsultation;
use App\Page;
use App\Article;
use App\Idea;
use Elasticsearch\Client;

class ReindexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:reindex';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Indexes all projects to Elasticsearch';

    /** @var \Elasticsearch\Client */
    private $client;

    /**
     * Create a new command instance.
     *
     * @param $client
     * @return void
     */
    public function __construct(Client $client)
    {
        parent::__construct();

        $this->client = $client;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->deleteIndex(new Project);
        $this->deleteIndex(new Proposal);
        $this->deleteIndex(new PublicConsultation);
        $this->deleteIndex(new Page);
        $this->deleteIndex(new Article);
        $this->deleteIndex(new Idea);

        $this->createIndex(new Project);
        $this->createIndex(new Proposal);
        $this->createIndex(new PublicConsultation);
        $this->createIndex(new Page);
        $this->createIndex(new Article);
        $this->createIndex(new Idea);

        $this->configIndexMapping(new Project);
        $this->configIndexMapping(new Proposal);
        $this->configIndexMapping(new PublicConsultation);
        $this->configIndexMapping(new Page);
        $this->configIndexMapping(new Article);
        $this->configIndexMapping(new Idea);

        $this->modelInstancesReindex(new Project);
        $this->modelInstancesReindex(new Proposal);
        $this->modelInstancesReindex(new PublicConsultation);
        $this->modelInstancesReindex(new Page);
        $this->modelInstancesReindex(new Article);
        $this->modelInstancesReindex(new Idea);

        $this->info('Done!');
    }

    private function createIndex($model)
    {
        $params = [
            'index' => $model->getSearchIndex()
        ];

        if(!$this->client->indices()->exists($params)) {
            $this->client->indices()->create($params);
        }
    }

    private function deleteIndex($model)
    {
        $params = [
            'index' => $model->getSearchIndex()
        ];

        if($this->client->indices()->exists($params)) {
            $this->client->indices()->delete($params);
        }
    }

    private function modelInstancesReindex($model)
    {
        $this->info('Reindexing all instances of ' . $model->getSearchType() . '.');

        foreach($model::cursor() as $instance) {
            $this->client->index([
                'index' => $instance->getSearchIndex(),
                'id' => $instance->getSearchType() . '-' . $instance->getKey(),
                'body' => $instance->toSearchArray()
            ]);
            $this->output->write('.');
        }
    }

    private function configIndexMapping($model)
    {
        $properties = $model->getSearchProperties();
        if($properties) {
            $params = [
                'index' => $model->getSearchIndex(),
                'body' => [
                    'properties' => $properties
                ]
            ];
            $this->client->indices()->putMapping($params);
        }
    }
}
