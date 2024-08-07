<?php

namespace App\Providers;

use App\Events\BulkMarkAddedEvent;
use App\Events\FeeChangedEvent;
use App\Events\ProgramChangeEvent;
use App\Events\StudentChangedEvent;
use App\Events\StudentResultChangedEvent;
use App\Events\StudentStatusChanged;
use App\Listeners\BulkMarkAddedListener;
use App\Listeners\FeeChangeListener;
use App\Listeners\LogStudentResultChangeListener;
use App\Listeners\ProgramChangeListener;
use App\Listeners\StudentChangeListener;
use App\Listeners\StudentStatusChangeListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [SendEmailVerificationNotification::class],
        StudentResultChangedEvent::class => [LogStudentResultChangeListener::class],
        FeeChangedEvent::class => [FeeChangeListener::class],
        StudentChangedEvent::class => [StudentChangeListener::class],
        BulkMarkAddedEvent::class => [BulkMarkAddedListener::class],
        ProgramChangeEvent::class => [ProgramChangeListener::class],
        StudentStatusChanged::class => [StudentStatusChangeListener::class],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
