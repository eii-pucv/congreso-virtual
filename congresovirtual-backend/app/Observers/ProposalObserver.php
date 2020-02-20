<?php

namespace App\Observers;

class ProposalObserver
{
    public function deleting($proposal)
    {
        if(!$proposal->isForceDeleting()) {
            $proposal->urgenciesRelated()->delete();
        }
    }

    public function restoring($proposal)
    {
        $proposal->urgenciesRelated()->restore();
    }
}
