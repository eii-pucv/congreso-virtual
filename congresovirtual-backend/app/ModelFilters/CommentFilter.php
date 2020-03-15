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

    public function body($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('comments.body', 'LIKE', "%$value%");
        });
    }

    public function state($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('comments.state', '=', $value);
        });
    }

    public function parent($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('comments.parent_id', '=', $value);
        });
    }

    public function project($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('comments.project_id', '=', $value);
        });
    }

    public function projectAllComments($value)
    {
        return $this
            ->select('comments.*')
            ->leftJoin('articles AS project_articles', 'comments.article_id', '=', 'project_articles.id')
            ->leftJoin('ideas AS project_ideas', 'comments.idea_id', '=', 'project_ideas.id')
            ->where(function($query) use ($value) {
                return $query->where('comments.project_id', '=', $value)
                    ->orWhere('project_articles.project_id', '=', $value)
                    ->orWhere('project_ideas.project_id', '=', $value);
            });
    }

    public function article($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('comments.article_id', '=', $value);
        });
    }

    public function idea($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('comments.idea_id', '=', $value);
        });
    }

    public function publicConsultation($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('comments.public_consultation_id', '=', $value);
        });
    }

    public function user($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('comments.user_id', '=', $value);
        });
    }

    public function query($value)
    {
        return $this->where(function($query) use ($value) {
            return $query->where('comments.body', 'LIKE', "%$value%");
        });
    }

    public function projectIsPublic($value)
    {
        return $this
            ->select('comments.*')
            ->leftJoin('projects', 'comments.project_id', '=', 'projects.id')
            ->where(function($query) use ($value) {
                return $query->where('projects.is_public', '=', $value)
                    ->orWhereNull('comments.project_id');
            });
    }

    public function articleIsPublic($value)
    {
        return $this
            ->select('comments.*')
            ->leftJoin('articles', 'comments.article_id', '=', 'articles.id')
            ->leftJoin('projects AS article_projects', 'articles.project_id', '=', 'article_projects.id')
            ->where(function($query) use ($value) {
                return $query->where('article_projects.is_public', '=', $value)
                    ->orWhereNull('comments.article_id');
            });
    }

    public function ideaIsPublic($value)
    {
        return $this
            ->select('comments.*')
            ->leftJoin('ideas', 'comments.idea_id', '=', 'ideas.id')
            ->leftJoin('projects AS idea_projects', 'ideas.project_id', '=', 'idea_projects.id')
            ->where(function($query) use ($value) {
                return $query->where('idea_projects.is_public', '=', $value)
                    ->orWhereNull('comments.idea_id');
            });
    }
}
