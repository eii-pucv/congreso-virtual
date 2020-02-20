<?php

namespace App\Search\Observers;

use Elasticsearch\Client;

class ProjectTermObserver
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function saved($projectTerm)
    {
        $this->updateProjectTerms($projectTerm);
    }

    public function deleted($projectTerm)
    {
        $this->updateProjectTerms($projectTerm);
    }

    private function updateProjectTerms($projectTerm)
    {
        $project = $projectTerm->project;
        $params = [];
        $params['body'][] = [
            'update' => [
                '_index' => $project->getSearchIndex(),
                '_id' => $project->getSearchType() . '-' . $project->getKey()
            ]
        ];
        $params['body'][] = [
            'doc' => $project->fresh()->toSearchArray()
        ];

        $terms = $project->toSearchArray()['terms'];
        foreach($project->articles as $article) {
            $params['body'][] = [
                'update' => [
                    '_index' => $article->getSearchIndex(),
                    '_id' => $article->getSearchType() . '-' . $article->getKey()
                ]
            ];
            $params['body'][] = [
                'doc' => [
                    'project' => $project->fresh()->toSearchArray(),
                    'terms' => $terms
                ]
            ];
        }
        foreach($project->ideas as $idea) {
            $params['body'][] = [
                'update' => [
                    '_index' => $idea->getSearchIndex(),
                    '_id' => $idea->getSearchType() . '-' . $idea->getKey()
                ]
            ];
            $params['body'][] = [
                'doc' => [
                    'project' => $project->fresh()->toSearchArray(),
                    'terms' => $terms
                ]
            ];
        }

        if($params) {
            $this->client->bulk($params);
        }
    }
}
