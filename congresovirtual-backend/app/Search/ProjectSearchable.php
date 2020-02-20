<?php

namespace App\Search;

use App\Search\Observers\ProjectIndexObserver;

trait ProjectSearchable
{
    use Searchable;

    protected $useSearchIndex = 'general';
    protected $useSearchType = 'projects';
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
        'postulante' => [
            'type' => 'text',
            'fields' => [
                'keyword' => [
                    'type' => 'keyword'
                ]
            ]
        ],
        'estado' => [
            'type' => 'keyword'
        ],
        'etapa' => [
            'type' => 'integer',
            'null_value' => -1
        ],
        'detalle' => [
            'type' => 'text'
        ],
        'resumen' => [
            'type' => 'text'
        ],
        'fecha_inicio' => [
            'type' => 'date',
            'format' => 'yyyy-MM-dd HH:mm:ss'
        ],
        'fecha_termino' => [
            'type' => 'date',
            'format' => 'yyyy-MM-dd HH:mm:ss'
        ],
        'boletin' => [
            'type' => 'text',
            'fields' => [
                'keyword' => [
                    'type' => 'keyword'
                ]
            ]
        ],
        'is_principal' => [
            'type' => 'boolean'
        ],
        'is_public' => [
            'type' => 'boolean'
        ],
        'is_enabled' => [
            'type' => 'boolean'
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
        'video' => [
            'type' => 'keyword',
            'index' => false
        ],
        'imagen_id' => [
            'type' => 'long',
            'index' => false
        ],
        'imagen' => [
            'type' => 'keyword',
            'index' => false
        ]
    ];

    public static function bootSearchable()
    {
        if(config('services.search.enabled')) {
            static::observe(ProjectIndexObserver::class);
        }
    }

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
