<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\OwnerApprovedCollectionRights' => [
            'App\Listeners\EmailUserAboutCollectionApproval',
        ],
        'App\Events\TermChanged' => [
            'App\Listeners\EmailUserAboutTermLinkChange',
        ],
        'App\Events\UserBookmarkedCollection' => [
            'App\Listeners\EmailOwnerAboutCollectionBookmark',
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
