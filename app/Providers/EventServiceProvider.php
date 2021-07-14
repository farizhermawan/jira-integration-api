<?php

namespace App\Providers;

use App\Events\FileDeleted;
use App\Events\UserCreated;
use App\Events\UserUpdated;
use App\Listeners\AttachNewUserToDefaultRole;
use App\Listeners\GenerateMembershipId;
use App\Listeners\RemoveFileFromDisk;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class EventServiceProvider extends ServiceProvider
{
  /**
   * The event listener mappings for the application.
   *
   * @var array
   */
  protected $listen = [
    SocialiteWasCalled::class => [
      'SocialiteProviders\\Google\\GoogleExtendSocialite@handle',
      'SocialiteProviders\\Yahoo\\YahooExtendSocialite@handle',
    ],
    Registered::class => [
      SendEmailVerificationNotification::class,
    ],
    UserCreated::class => [
      AttachNewUserToDefaultRole::class,
      GenerateMembershipId::class,
    ],
    UserUpdated::class => [
      GenerateMembershipId::class,
    ],
    FileDeleted::class => [
      RemoveFileFromDisk::class,
    ]
  ];

  /**
   * Register any events for your application.
   *
   * @return void
   */
  public function boot()
  {
    parent::boot();

    //
  }
}
