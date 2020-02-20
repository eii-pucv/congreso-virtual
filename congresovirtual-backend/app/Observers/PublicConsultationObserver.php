<?php

namespace App\Observers;

use App\File;

class PublicConsultationObserver
{
    public function deleting($publicConsultation)
    {
        $publicConsultationFiles = $publicConsultation->files(true)->pluck('id');

        if(!$publicConsultation->isForceDeleting()) {
            foreach($publicConsultation->comments as $comment) {
                $comment->delete();
            }

            $publicConsultation->votes()->delete();
            File::whereIn('id', $publicConsultationFiles)->delete();
        } else {
            $publicConsultationComments = $publicConsultation->comments()->withTrashed()->get();
            foreach($publicConsultationComments as $comment) {
                $comment->forceDelete();
            }

            File::whereIn('id', $publicConsultationFiles)->forceDelete();
            $publicConsultationDirectory = "consultations/{$publicConsultation->id}";
            \Storage::deleteDirectory($publicConsultationDirectory);
        }
    }

    public function restoring($publicConsultation)
    {
        $publicConsultationComments = $publicConsultation->comments()->withTrashed()->get();
        foreach($publicConsultationComments as $comment) {
            $comment->restore();
        }

        $publicConsultation->votes()->restore();
        $publicConsultationFiles = $publicConsultation->files(true)->pluck('id');
        File::withTrashed()->whereIn('id', $publicConsultationFiles)->restore();
    }
}
