<?php

declare(strict_types=1);

namespace App\Providers;

use App\Notifications\QueueFailed;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        if (app()->environment('testing', 'staging', 'production')) {
            Queue::failing(function (JobFailed $event): void {
                Notification::route('mail', config('app.system.mail.address'))
                    ->route('slack', config('app.system.slack.webhook'))
                    ->notify(new QueueFailed($event));
            });
        }

        Password::defaults(function () {
            $rule = Password::min(8)->letters()->numbers()->rules('max:20');

            return $this->app->isProduction()
                        ? $rule->uncompromised()
                        : $rule;
        });
    }
}
