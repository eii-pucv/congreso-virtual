<?php

namespace App\Observers;

use App\File;

class UserObserver
{
    public function deleting($user)
    {
        $userFiles = $user->files(true)->pluck('id');

        if(!$user->isForceDeleting()) {
            foreach($user->comments as $comment) {
                $comment->delete();
            }

            foreach($user->votes as $vote) {
                $vote->delete();
            }

            foreach($user->urgencies as $urgency) {
                $urgency->delete();
            }

            $user->player()->delete();
            $user->locationOrgs()->delete();
            $user->memberOrgs()->delete();
            $user->proposals()->delete();
            $user->denounces()->delete();
            $user->userMetas()->delete();
            File::whereIn('id', $userFiles)->delete();
        } else {
            $userComments = $user->comments()->withTrashed()->get();
            foreach($userComments as $comment) {
                $comment->forceDelete();
            }

            foreach($user->votes as $vote) {
                $vote->delete();
            }

            foreach($user->urgencies as $urgency) {
                $urgency->delete();
            }

            File::whereIn('id', $userFiles)->forceDelete();
            $userDirectory = "users/{$user->id}";
            \Storage::deleteDirectory($userDirectory);
        }
    }

    public function restoring($user)
    {
        $userComments = $user->comments()->withTrashed()->get();
        foreach($userComments as $comment) {
            $comment->restore();
        }

        $userVotes = $user->votes()->onlyTrashed()->get();
        foreach($userVotes as $vote) {
            $vote->restore();
        }

        $userUrgencies = $user->urgencies()->onlyTrashed()->get();
        foreach($userUrgencies as $urgency) {
            $urgency->restore();
        }

        $user->player()->restore();
        $user->locationOrgs()->restore();
        $user->memberOrgs()->restore();
        $user->proposals()->restore();
        $user->denounces()->restore();
        $user->userMetas()->restore();
        $userFiles = $user->files(true)->pluck('id');
        File::withTrashed()->whereIn('id', $userFiles)->restore();
    }
}
