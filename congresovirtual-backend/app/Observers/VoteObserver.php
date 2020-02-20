<?php

namespace App\Observers;

use App\Project;
use App\Article;
use App\Idea;
use App\Comment;
use App\PublicConsultation;

class VoteObserver
{
    public function creating($vote)
    {
        if(isset($vote->project_id) && $vote->project_id !== null) {
            Project::incrementingCountVotes($vote);
        } else if(isset($vote->article_id) && $vote->article_id !== null) {
            Article::incrementingCountVotes($vote);
        } else if(isset($vote->idea_id) && $vote->idea_id !== null) {
            Idea::incrementingCountVotes($vote);
        } else if(isset($vote->comment_id) && $vote->comment_id !== null) {
            Comment::incrementingCountVotes($vote);
        } else if(isset($vote->public_consultation_id) && $vote->public_consultation_id !== null) {
            PublicConsultation::incrementingCountVotes($vote);
        } else {
            throw new \Exception();
        }
    }

    public function updating($vote)
    {
        if(isset($vote->project_id) && $vote->project_id !== null) {
            Project::incrementingCountVotes($vote);
            Project::decrementingCountVotes($vote);
        } else if(isset($vote->article_id) && $vote->article_id !== null) {
            Article::incrementingCountVotes($vote);
            Article::decrementingCountVotes($vote);
        } else if(isset($vote->idea_id) && $vote->idea_id !== null) {
            Idea::incrementingCountVotes($vote);
            Idea::decrementingCountVotes($vote);
        } else if(isset($vote->comment_id) && $vote->comment_id !== null) {
            Comment::incrementingCountVotes($vote);
            Comment::decrementingCountVotes($vote);
        } else if(isset($vote->public_consultation_id) && $vote->public_consultation_id !== null) {
            PublicConsultation::incrementingCountVotes($vote);
            PublicConsultation::decrementingCountVotes($vote);
        } else {
            throw new \Exception();
        }
    }

    public function deleting($vote)
    {
        if(isset($vote->project_id) && $vote->project_id !== null) {
            Project::decrementingCountVotes($vote);
        } else if(isset($vote->article_id) && $vote->article_id !== null) {
            Article::decrementingCountVotes($vote);
        } else if(isset($vote->idea_id) && $vote->idea_id !== null) {
            Idea::decrementingCountVotes($vote);
        } else if(isset($vote->comment_id) && $vote->comment_id !== null) {
            Comment::decrementingCountVotes($vote);
        } else if(isset($vote->public_consultation_id) && $vote->public_consultation_id !== null) {
            PublicConsultation::decrementingCountVotes($vote);
        } else {
            throw new \Exception();
        }
    }
}
