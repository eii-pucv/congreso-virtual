<?php

namespace App\Search;

use App\Search\Observers\IndexObserver;

trait PageSearchable
{
    use Searchable;

    protected $useSearchIndex = 'general';
    protected $useSearchType = 'pages';
    protected $useSearchProperties = [
        'id' => [
            'type' => 'long',
            'index' => false
        ],
        'title' => [
            'type' => 'text',
            'fields' => [
                'keyword' => [
                    'type' => 'keyword'
                ]
            ]
        ],
        'slug' => [
            'type' => 'text',
            'fields' => [
                'keyword' => [
                    'type' => 'keyword'
                ]
            ]
        ],
        'content' => [
            'type' => 'text'
        ],
        'is_public' => [
            'type' => 'boolean'
        ]
    ];

    public function getSearchProperties()
    {
        if(property_exists($this, 'useSearchProperties')) {
            $propertiesArray = $this->useSearchProperties;
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
        $array = $this->makeHidden(['created_at', 'updated_at', 'deleted_at'])->toArray();
        $array['terms'] = $this->terms->makeHidden(['created_at', 'updated_at', 'deleted_at', 'pivot', 'taxonomies']);
        $array['_object_type'] = $this->getSearchType();
        return $array;
    }
}
