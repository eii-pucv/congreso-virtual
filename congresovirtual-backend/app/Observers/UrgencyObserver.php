<?php

namespace App\Observers;

use App\Proposal;

class UrgencyObserver
{
    public function creating($urgency)
    {
        if(isset($urgency->proposal_id) && $urgency->proposal_id !== null) {
            Proposal::incrementingCountUrgencies($urgency);
        } else {
            throw new \Exception();
        }
    }

    public function updating($urgency)
    {
        if(isset($urgency->proposal_id) && $urgency->proposal_id !== null) {
            Proposal::incrementingCountUrgencies($urgency);
            Proposal::decrementingCountUrgencies($urgency);
        } else {
            throw new \Exception();
        }
    }

    public function deleting($urgency)
    {
        if(isset($urgency->proposal_id) && $urgency->proposal_id !== null) {
            Proposal::decrementingCountUrgencies($urgency);
        } else {
            throw new \Exception();
        }
    }
}
