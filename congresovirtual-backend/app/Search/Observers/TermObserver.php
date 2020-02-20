<?php

namespace App\Search\Observers;

use Elasticsearch\Client;

class TermObserver
{
    protected $client;
    private $projects;
    private $publicConsultations;
    private $pages;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->projects = [];
        $this->publicConsultations = [];
        $this->pages = [];
    }

    public function updated($term)
    {
        $this->updateTermsInRelatedEntities($term);
    }

    public function deleting($term)
    {
        if($term->isForceDeleting()) {
            $this->projects = $term->projects;
            $this->publicConsultations = $term->publicConsultations;
        }
    }

    public function deleted($term)
    {
        if($term->isForceDeleting()) {
            $this->updateTermsInRelatedEntities($term, $this->projects, $this->publicConsultations, $this->pages);
        } else {
            $this->updateTermsInRelatedEntities($term);
        }
    }

    public function restored($term)
    {
        $this->updateTermsInRelatedEntities($term);
    }

    public function updateTermsInRelatedEntities($term, $projects = null, $publicConsultations = null, $pages = null)
    {
        $params = [];

        if(!$projects) {
            $projects = $term->projects;
        }
        foreach($projects as $project) {
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
        }

        if(!$publicConsultations) {
            $publicConsultations = $term->publicConsultations;
        }
        foreach($publicConsultations as $publicConsultation) {
            $params['body'][] = [
                'update' => [
                    '_index' => $publicConsultation->getSearchIndex(),
                    '_id' => $publicConsultation->getSearchType() . '-' . $publicConsultation->getKey()
                ]
            ];
            $params['body'][] = [
                'doc' => $publicConsultation->fresh()->toSearchArray()
            ];
        }

        if(!$pages) {
            $pages = $term->pages;
        }
        foreach($pages as $page) {
            $params['body'][] = [
                'update' => [
                    '_index' => $page->getSearchIndex(),
                    '_id' => $page->getSearchType() . '-' . $page->getKey()
                ]
            ];
            $params['body'][] = [
                'doc' => $page->fresh()->toSearchArray()
            ];
        }

        if($params) {
            $this->client->bulk($params);
        }
    }
}
