<?php

namespace App\Search;

trait ProposalSearchable
{
    use Searchable;

    protected $useSearchIndex = 'general';
    protected $useSearchType = 'proposals';
    protected $useSearchProperties = [
        'id' => [
            'type' => 'long',
            'index' => false
        ],
        'titulo' => [
            'type' => 'text',
            'fields' => [
                'keyword' => [
                    'type' => 'keyword'
                ]
            ]
        ],
        'detalle' => [
            'type' => 'text'
        ],
        'autoria' => [
            'type' => 'text',
            'fields' => [
                'keyword' => [
                    'type' => 'keyword'
                ]
            ]
        ],
        'boletin' => [
            'type' => 'text',
            'fields' => [
                'keyword' => [
                    'type' => 'keyword'
                ]
            ]
        ],
        'fecha_ingreso' => [
            'type' => 'date',
            'format' => 'yyyy-MM-dd'
        ],
        'state' => [
            'type' => 'integer'
        ],
        'type' => [
            'type' => 'integer'
        ],
        'is_public' => [
            'type' => 'boolean'
        ],
        'petitions' => [
            'type' => 'integer',
            'index' => false
        ],
        'urgencies' => [
            'type' => 'integer',
            'index' => false
        ],
        'argument' => [
            'type' => 'text'
        ],
        'video_url' => [
            'type' => 'keyword',
            'index' => false
        ],
        'video_source' => [
            'type' => 'keyword',
            'index' => false
        ],
        'user_id' => [
            'type' => 'long',
            'index' => false
        ]
    ];

    public function toSearchArray()
    {
        $array = $this->makeHidden(['created_at', 'updated_at', 'deleted_at'])->toArray();
        $array['_object_type'] = $this->getSearchType();
        return $array;
    }
}
