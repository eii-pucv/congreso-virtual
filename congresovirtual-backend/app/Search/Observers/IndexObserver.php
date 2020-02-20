<?php

namespace App\Search\Observers;

use Elasticsearch\Client;

class IndexObserver
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function saved($model)
    {
        $this->client->index([
           'index' => $model->getSearchIndex(),
           'id' => $model->getSearchType() . '-' . $model->getKey(),
           'body' => $model->fresh()->toSearchArray()
        ]);
    }

    public function deleted($model)
    {
        $this->client->delete([
            'index' => $model->getSearchIndex(),
            'id' => $model->getSearchType() . '-' . $model->getKey()
        ]);
    }
}
