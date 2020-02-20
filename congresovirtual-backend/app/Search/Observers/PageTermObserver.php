<?php

namespace App\Search\Observers;

use Elasticsearch\Client;

class PageTermObserver
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function saved($pageTerm)
    {
        $this->updatePageTerms($pageTerm);
    }

    public function deleted($pageTerm)
    {
        $this->updatePageTerms($pageTerm);
    }

    private function updatePageTerms($pageTerm)
    {
        $page = $pageTerm->page;
        $params = [];
        $params['body'][] = [
            'update' => [
                '_index' => $page->getSearchIndex(),
                '_id' => $page->getSearchType() . '-' . $page->getKey()
            ]
        ];
        $params['body'][] = [
            'doc' => $page->fresh()->toSearchArray()
        ];

        if($params) {
            $this->client->bulk($params);
        }
    }
}
