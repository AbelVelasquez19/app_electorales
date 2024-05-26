<?php

namespace App\Listeners;

use App\Events\ReporteStatusChangedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ReporteStatusListener
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
     * @param  \App\Events\ReporteStatusChangedEvent  $event
     * @return void
     */
    public function handle(ReporteStatusChangedEvent $event)
    {
        //
    }
}
