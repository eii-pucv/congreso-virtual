<?php

namespace App\Search\Observers;

use Elasticsearch\Client;

class PublicConsultationTermObserver
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function saved($publicConsultationTerm)
    {
        $this->updatePublicConsultationTerms($publicConsultationTerm);
    }

    public function deleted($publicConsultationTerm)
    {
        $this->updatePublicConsultationTerms($publicConsultationTerm);
    }

    private function updatePublicConsultationTerms($publicConsultationTerm)
    {
        $publicConsultation = $publicConsultationTerm->publicConsultation;
        $params = [];
        $params['body'][] = [
            'update' => [
                '_index' => $publicConsultation->getSearchIndex(),
                '_id' => $publicConsultation->getSearchType() . '-' . $publicConsultation->getKey()
            ]
        ];
        $params['body'][] = [
            'doc' => $publicConsultation->fresh()->toSearchArray()
        ];

        if($params) {
            $this->client->bulk($params);
        }
    }
}
