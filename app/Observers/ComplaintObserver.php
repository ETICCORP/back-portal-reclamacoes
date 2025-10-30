<?php

namespace App\Observers;

use App\Models\Complaint\Complaint;
use App\Models\ComplaintTriages\ComplaintTriages;

class ComplaintObserver
{
    /**
     * Handle the Complaint "created" event.
     */
    public function created(Complaint $complaint)
    {
        $days = $complaint->client->deadline_days ?? 15; // configurable per client
        $start = now();
        $end = $start->copy()->addWeekdays($days);

        ComplaintTriages::create([
            'complaint_id' => $complaint->id,
            'days' => $days,
            'start_date' => $start,
            'end_date' => $end,
        ]);
    }


    /**
     * Handle the Complaint "updated" event.
     */
    public function updated(Complaint $complaint): void
    {
        //
    }

    /**
     * Handle the Complaint "deleted" event.
     */
    public function deleted(Complaint $complaint): void
    {
        //
    }

    /**
     * Handle the Complaint "restored" event.
     */
    public function restored(Complaint $complaint): void
    {
        //
    }

    /**
     * Handle the Complaint "force deleted" event.
     */
    public function forceDeleted(Complaint $complaint): void
    {
        //
    }
}
