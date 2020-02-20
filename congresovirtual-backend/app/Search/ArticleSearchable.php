<?php

namespace App\Search;

use App\Project;

trait ArticleSearchable
{
    use Searchable;

    protected $useSearchIndex = 'general';
    protected $useSearchType = 'articles';
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
        'votos_a_favor' => [
            'type' => 'integer',
            'index' => false
        ],
        'votos_en_contra' => [
            'type' => 'integer',
            'index' => false
        ],
        'abstencion' => [
            'type' => 'integer',
            'index' => false
        ],
        'project_id' => [
            'type' => 'long',
            'index' => false
        ],
        'is_public' => [
            'type' => 'boolean'
        ]
    ];

    public function getSearchProperties()
    {
        if(property_exists($this, 'useSearchProperties')) {
            $propertiesArray = $this->useSearchProperties;
            $propertiesArray['project'] = [
                'type' => 'object',
                'properties' => (new Project)->getSearchProperties()
            ];
            $propertiesArray['terms'] = [
                'type' => 'nested',
                'properties' => [
                    'id' => [
                        'type' => 'long'
                    ],
                    'value' => [
                        'type' => 'keyword'
                    ],
                    'parent_id' => [
                        'type' => 'long',
                        'index' => false
                    ]
                ]
            ];
            $propertiesArray['_object_type'] = [
                'type' => 'keyword'
            ];
            return $propertiesArray;
        }
        return null;
    }

    public function toSearchArray()
    {
        $project = $this->project()->withTrashed()->first();

        $array = $this->makeHidden(['created_at', 'updated_at', 'deleted_at'])->toArray();
        $array['project'] = $project->toSearchArray();
        $array['terms'] = $project->terms->makeHidden(['created_at', 'updated_at', 'deleted_at', 'pivot', 'taxonomies']);
        $array['_object_type'] = $this->getSearchType();
        return $array;
    }
}
