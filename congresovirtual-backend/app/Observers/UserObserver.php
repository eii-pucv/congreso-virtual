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

            $user->locationOrgs()->delete();
            $user->memberOrgs()->delete();
            $user->votes()->delete();
            $user->urgencies()->delete();
            $user->proposals()->delete();
            $user->denounces()->delete();
            $user->userMetas()->delete();
            File::whereIn('id', $userFiles)->delete();
        } else {
            $userComments = $user->comments()->withTrashed()->get();
            foreach($userComments as $comment) {
                $comment->forceDelete();
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

        $user->locationOrgs()->restore();
        $user->memberOrgs()->restore();
        $user->votes()->restore();
        $user->urgencies()->restore();
        $user->proposals()->restore();
        $user->denounces()->restore();
        $user->userMetas()->restore();
        $userFiles = $user->files(true)->pluck('id');
        File::withTrashed()->whereIn('id', $userFiles)->restore();
    }
}
