<?php

namespace App\Search;

trait PublicConsultationSearchable
{
    use Searchable;

    protected $useSearchIndex = 'general';
    protected $useSearchType = 'consultations';
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
        'autor' => [
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
        'votos_a_favor' => [
            'type' => 'integer',
            'index' => false
        ],
        'votos_en_contra' => [
            'type' => 'integer',
            'index' => false
        ],
        'icono' => [
            'type' => 'keyword',
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
        $array['terms'] = $this->terms->makeHidden(['created_at', 'updated_at', 'deleted_at', 'pivot']);
        $array['_object_type'] = $this->getSearchType();
        return $array;
    }
}
