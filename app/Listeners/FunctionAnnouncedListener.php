<?php

namespace App\Listeners;

use App\Events\FunctionAnnounced;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class FunctionAnnouncedListener implements ShouldBroadcast
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
     * @param  object  $event
     * @return void
     */
    public function handle(FunctionAnnounced $event)
    {
        // broadcast($event)->to('announcements');
    }

    public function broadcastOn()
    {
        return new Channel('announcements');
    }
}
