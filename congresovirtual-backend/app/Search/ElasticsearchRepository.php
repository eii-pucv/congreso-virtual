<?php

namespace App\Search;

use Elasticsearch\Client;
use Illuminate\Support\Arr;

class ElasticsearchRepository
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    private function getBodySearch($query = '', $etapas = [], $termsIds = [], $isAvailableVoting = null, $includeEtapas = [], $objectsTypes = [], $sortAttribute = null, $sortOrder = 'asc')
    {
        $bodySearch = [
            'query' => [
                'bool' => [
                    'must_not' => [
                        ['term' => [ 'is_public' => false ]],
                        ['term' => [ 'state' => 0 ]]
                    ]
                ]
            ]
        ];

        if($query) {
            $bodySearch['query']['bool']['must'][] = [
                'multi_match' => [
                    'fields' => [
                        'titulo', 'postulante', 'autoria', 'autor', 'detalle', 'resumen', 'boletin', 'argument', 'title', 'slug', 'content'
                    ],
                    'query' => $query
                ]
            ];
        }
        if($etapas) {
            $bodySearch['query']['bool']['filter'][] = [
                'terms' => [ 'etapa' => $etapas ]
            ];
        }
        if($termsIds) {
            $bodySearch['query']['bool']['must'][] = [
                'nested' => [
                    'path' => 'terms',
                    'query' => [
                        'bool' => [
                            'filter' => [
                                ['terms' => [ 'terms.id' => $termsIds ]]
                            ]
                        ]
                    ]
                ]
            ];
        }
        if($isAvailableVoting === true) {
            $bodySearch['query']['bool']['must_not'] = array_merge(
                $bodySearch['query']['bool']['must_not'],
                [
                    ['term' => [ 'etapa' => 3 ]],
                    ['term' => [ 'is_enabled' => false ]],
                    ['term' => [ 'estado' => 0 ]],
                    ['range' => [ 'fecha_inicio' => [ 'gt' => 'now' ] ]],
                    ['range' => [ 'fecha_termino' => [ 'lt' => 'now' ] ]]
                ]
            );
        } else if($isAvailableVoting === false) {
            $bodySearch['query']['bool']['minimum_should_match'] = 1;
            $bodySearch['query']['bool']['should'] = [
                ['term' => [ 'state' => 0 ]],
                ['term' => [ 'estado' => 0 ]],
                ['term' => [ 'etapa' => 3 ]],
                ['term' => [ 'is_enabled' => false ]],
                ['range' => [ 'fecha_inicio' => [ 'gt' => 'now' ] ]],
                ['range' => [ 'fecha_termino' => [ 'lt' => 'now' ] ]]
            ];

            if($includeEtapas) {
                $bodySearch['query']['bool']['should'] = array_merge(
                    $bodySearch['query']['bool']['should'],
                    [['terms' => [ 'etapa' => $includeEtapas ] ]]
                );
            }
        }

        if($objectsTypes) {
            $bodySearch['query']['bool']['filter'][] = [
                'terms' => [ '_object_type' => $objectsTypes ]
            ];
        }
        if($sortAttribute) {
            $bodySearch['sort'] = [
                [ $sortAttribute => $sortOrder ]
            ];
        }

        return $bodySearch;
    }

    public function searchWithScroll($query = '', $etapas = [], $termsIds = [], $isAvailableVoting = null, $includeEtapas = [], $objectsTypes = [], $size = 10, $sortAttribute = null, $sortOrder = 'asc', $timeout = '30s', $scrollId = null)
    {
        if($scrollId === null) {
            $response = $this->initSearchOnElasticsearchWithScroll($query, $etapas, $termsIds, $isAvailableVoting, $includeEtapas, $objectsTypes, $size, $sortAttribute, $sortOrder, $timeout);
        } else {
            $response = $this->continueSearchOnElasticsearchWithScroll($scrollId, $timeout);
        }
        return [
            'total_results' => $response['hits']['total']['value'],
            'returned_results' => count($response['hits']['hits']),
            'scroll_id' => $response['_scroll_id'],
            'results' => Arr::pluck($response['hits']['hits'], '_source')
        ];
    }

    public function searchWithFromAndSize($query = '', $etapas = [], $termsIds = [], $isAvailableVoting = null, $includeEtapas = [], $objectsTypes = [], $from = 0, $size = 10, $sortAttribute = null, $sortOrder = 'asc')
    {
        $response = $this->searchOnElasticsearchWithFromAndSize($query, $etapas, $termsIds, $isAvailableVoting, $includeEtapas, $objectsTypes, $from, $size, $sortAttribute, $sortOrder);
        return [
            'total_results' => $response['hits']['total']['value'],
            'returned_results' => count($response['hits']['hits']),
            'results' => Arr::pluck($response['hits']['hits'], '_source')
        ];
    }

    private function initSearchOnElasticsearchWithScroll($query = '', $etapas = [], $termsIds = [], $isAvailableVoting = null, $includeEtapas = [], $objectsTypes = [], $size = 10, $sortAttribute = null, $sortOrder = 'asc', $timeout = '30s')
    {
        $params = [
            'index' => 'general',
            'scroll' => $timeout,
            'size' => $size,
            'body' => $this->getBodySearch($query, $etapas, $termsIds, $isAvailableVoting, $includeEtapas, $objectsTypes, $sortAttribute, $sortOrder)
        ];
        return $this->client->search($params);
    }

    private function continueSearchOnElasticsearchWithScroll($scrollId, $timeout = '30s')
    {
        $params = [
            'scroll_id' => $scrollId,
            'scroll' => $timeout
        ];
        return $this->client->scroll($params);
    }

    private function searchOnElasticsearchWithFromAndSize($query = '', $etapas = [], $termsIds = [], $isAvailableVoting = null, $includeEtapas = [], $objectsTypes = [], $from = 0, $size = 10, $sortAttribute = null, $sortOrder = 'asc')
    {
        $params = [
            'index' => 'general',
            'from' => $from,
            'size' => $size,
            'body' => $this->getBodySearch($query, $etapas, $termsIds, $isAvailableVoting, $includeEtapas, $objectsTypes, $sortAttribute, $sortOrder)
        ];
        return $this->client->search($params);
    }
}
