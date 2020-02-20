<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class CommentFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function body($name)
    {
        return $this->where(function($q) use ($name)
        {
            return $q->where('comments.body', 'LIKE', "%$name%");
        });
    }

    public function parent($name) {
        return $this
            ->where('comments.parent_id', '=', $name);
    }

    public function project($name) {
        return $this
            ->where('comments.project_id', '=', $name);
    }

    public function article($name) {
        return $this
            ->where('comments.article_id', '=', $name);
    }

    public function idea($name) {
        return $this
            ->where('comments.idea_id', '=', $name);
    }

    public function publicConsultation($name) {
        return $this
            ->where('comments.public_consultation_id', '=', $name);
    }

    public function user($name) {
        return $this
            ->where('comments.user_id', '=', $name);
    }

    public function query($name)
    {
        return $this
            ->where('comments.body', 'LIKE', "%$name%");
    }

    public function projectIsPublic($name)
    {
        return $this
            ->select('comments.*')
            ->leftJoin('projects', 'comments.project_id', '=', 'projects.id')
            ->where(function ($query) use ($name) {
                return $query->where('projects.is_public', '=', $name)
                    ->orWhereNull('comments.project_id');
            });
    }

    public function articleIsPublic($name)
    {
        return $this
            ->select('comments.*')
            ->leftJoin('articles', 'comments.article_id', '=', 'articles.id')
            ->leftJoin('projects AS article_projects', 'articles.project_id', '=', 'article_projects.id')
            ->where(function ($query) use ($name) {
                return $query->where('article_projects.is_public', '=', $name)
                    ->orWhereNull('comments.article_id');
            });
    }

    public function ideaIsPublic($name)
    {
        return $this
            ->select('comments.*')
            ->leftJoin('ideas', 'comments.idea_id', '=', 'ideas.id')
            ->leftJoin('projects AS idea_projects', 'ideas.project_id', '=', 'idea_projects.id')
            ->where(function ($query) use ($name) {
                return $query->where('idea_projects.is_public', '=', $name)
                    ->orWhereNull('comments.idea_id');
            });
    }
}
