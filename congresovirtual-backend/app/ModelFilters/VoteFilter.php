<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class VoteFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function project($name) {
        return $this
            ->where('votes.project_id', '=', $name);
    }

    public function article($name) {
        return $this
            ->where('votes.article_id', '=', $name);
    }

    public function idea($name) {
        return $this
            ->where('votes.idea_id', '=', $name);
    }

    public function comment($name) {
        return $this
            ->where('votes.comment_id', '=', $name);
    }

    public function publicConsultation($name) {
        return $this
            ->where('votes.public_consultation_id', '=', $name);
    }

    public function user($name) {
        return $this
            ->where('votes.user_id', '=', $name);
    }

    public function projectIsPublic($name)
    {
        return $this
            ->select('votes.*')
            ->leftJoin('projects AS vote_projects', 'votes.project_id', '=', 'vote_projects.id')
            ->where(function ($query) use ($name) {
                return $query->where('vote_projects.is_public', '=', $name)
                    ->orWhereNull('votes.project_id');
            });
    }

    public function articleIsPublic($name)
    {
        return $this
            ->select('votes.*')
            ->leftJoin('articles AS vote_articles', 'votes.article_id', '=', 'vote_articles.id')
            ->leftJoin('projects AS article_projects', 'vote_articles.project_id', '=', 'article_projects.id')
            ->where(function ($query) use ($name) {
                return $query->where('article_projects.is_public', '=', $name)
                    ->orWhereNull('votes.article_id');
            });
    }

    public function ideaIsPublic($name)
    {
        return $this
            ->select('votes.*')
            ->leftJoin('ideas AS vote_ideas', 'votes.idea_id', '=', 'vote_ideas.id')
            ->leftJoin('projects AS idea_projects', 'vote_ideas.project_id', '=', 'idea_projects.id')
            ->where(function ($query) use ($name) {
                return $query->where('idea_projects.is_public', '=', $name)
                    ->orWhereNull('votes.idea_id');
            });
    }

    public function commentState($name)
    {
        return $this
            ->select('votes.*')
            ->leftJoin('comments', 'votes.comment_id', '=', 'comments.id')
            ->where(function ($query) use ($name) {
                return $query->where('comments.state', '=', $name)
                    ->orWhereNull('votes.comment_id');
            });
    }

    public function commentProjectIsPublic($name)
    {
        return $this
            ->select('votes.*')
            ->leftJoin('comments AS vote_comments', 'votes.comment_id', '=', 'vote_comments.id')
            ->leftJoin('projects', 'vote_comments.project_id', '=', 'projects.id')
            ->where(function ($query) use ($name) {
                return $query->where('projects.is_public', '=', $name)
                    ->orWhereNull('vote_comments.project_id');
            });
    }

    public function commentArticleIsPublic($name)
    {
        return $this
            ->select('votes.*')
            ->leftJoin('comments AS vote_comments_2', 'votes.comment_id', '=', 'vote_comments_2.id')
            ->leftJoin('articles', 'vote_comments_2.article_id', '=', 'articles.id')
            ->leftJoin('projects AS article_projects_2', 'articles.project_id', '=', 'article_projects_2.id')
            ->where(function ($query) use ($name) {
                return $query->where('article_projects_2.is_public', '=', $name)
                    ->orWhereNull('vote_comments_2.article_id');
            });
    }

    public function commentIdeaIsPublic($name)
    {
        return $this
            ->select('votes.*')
            ->leftJoin('comments AS vote_comments_3', 'votes.comment_id', '=', 'vote_comments_3.id')
            ->leftJoin('ideas', 'vote_comments_3.idea_id', '=', 'ideas.id')
            ->leftJoin('projects AS idea_projects_2', 'ideas.project_id', '=', 'idea_projects_2.id')
            ->where(function ($query) use ($name) {
                return $query->where('idea_projects_2.is_public', '=', $name)
                    ->orWhereNull('vote_comments_3.idea_id');
            });
    }
}
