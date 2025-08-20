<?php

namespace App\Providers;

use App\Channels\RedisChannel;
use App\Events\MessageSent;
use App\Interfaces\PaymentGatewayInterface;
use App\Listeners\SendMessageNotification;
use App\Models\Message;
use App\Observers\MessageObserver;
use App\Services\StripePaymentService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{


    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

       $this->app->bind(PaymentGatewayInterface::class,StripePaymentService::class);


        Message::observe(MessageObserver::class);
        $this->app->make(ChannelManager::class)->extend('redis', function ($app) {
            return new RedisChannel();
        });

         // Manually attach event → listener
        //  Event::listen(
        //     MessageSent::class,
        //     [SendMessageNotification::class, 'handle']
        // );
    }
}
