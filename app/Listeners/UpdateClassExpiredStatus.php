<?php

namespace App\Listeners;

use App\Events\ClassExpired;
use DateTime;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateClassExpiredStatus
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ClassExpired  $event
     * @return void
     */
    public function handle(ClassExpired $event)
    {
        $class = $event->class;
        $endDate = $class->classEndDate;
        $currentDate = date('Y-m-d');
        $endDateTime = new DateTime($endDate);
        $currentDateTime = new DateTime($currentDate);

        if ($currentDateTime > $endDateTime) {
            $class->update(['expired' => 1]);
        }
    }
}
