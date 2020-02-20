<?php

namespace App\Observers;

use App\File;

class ProjectObserver
{
    public function deleting($project)
    {
        $projectFiles = $project->files(true)->pluck('id');

        if(!$project->isForceDeleting()) {
            foreach($project->articles as $article) {
                $article->delete();
            }
            foreach($project->ideas as $idea) {
                $idea->delete();
            }
            foreach($project->comments as $comment) {
                $comment->delete();
            }

            $project->votes()->delete();
            File::whereIn('id', $projectFiles)->delete();
        } else {
            $projectArticles = $project->articles()->withTrashed()->get();
            foreach($projectArticles as $article) {
                $article->forceDelete();
            }
            $projectIdeas = $project->ideas()->withTrashed()->get();
            foreach($projectIdeas as $idea) {
                $idea->forceDelete();
            }
            $projectComments = $project->comments()->withTrashed()->get();
            foreach($projectComments as $comment) {
                $comment->forceDelete();
            }

            File::whereIn('id', $projectFiles)->forceDelete();
            $projectDirectory = "projects/{$project->id}";
            \Storage::deleteDirectory($projectDirectory);
        }
    }

    public function restoring($project)
    {
        $projectArticles = $project->articles()->withTrashed()->get();
        foreach($projectArticles as $article) {
            $article->restore();
        }
        $projectIdeas = $project->ideas()->withTrashed()->get();
        foreach($projectIdeas as $idea) {
            $idea->restore();
        }
        $projectComments = $project->comments()->withTrashed()->get();
        foreach($projectComments as $comment) {
            $comment->restore();
        }
        
        $project->votes()->restore();
        $projectFiles = $project->files(true)->pluck('id');
        File::withTrashed()->whereIn('id', $projectFiles)->restore();
    }
}
