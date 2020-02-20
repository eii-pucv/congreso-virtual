<?php

namespace App\Search\Observers;

class ProjectIndexObserver extends IndexObserver
{
    public function updating($model)
    {
        $params = [];
        $params['body'][] = [
            'update' => [
                '_index' => $model->getSearchIndex(),
                '_id' => $model->getSearchType() . '-' . $model->getKey()
            ]
        ];
        $params['body'][] = [
            'doc' => $model->fresh()->toSearchArray()
        ];

        foreach($model->articles as $article) {
            $params['body'][] = [
                'update' => [
                    '_index' => $article->getSearchIndex(),
                    '_id' => $article->getSearchType() . '-' . $article->getKey()
                ]
            ];
            $params['body'][] = [
                'doc' => [
                    'is_public' => $model->is_public,
                    'project' => $model->fresh()->toSearchArray()
                ]
            ];
        }
        foreach($model->ideas as $idea) {
            $params['body'][] = [
                'update' => [
                    '_index' => $idea->getSearchIndex(),
                    '_id' => $idea->getSearchType() . '-' . $idea->getKey()
                ]
            ];
            $params['body'][] = [
                'doc' => [
                    'is_public' => $model->is_public,
                    'project' => $model->fresh()->toSearchArray()
                ]
            ];
        }

        if($params) {
            $this->client->bulk($params);
        }
    }
}
