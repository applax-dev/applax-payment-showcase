<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Check order status every 5 minutes
        $schedule->command('orders:check-status')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->description('Check and update order statuses from Gateway');

        // Clean up showcase demo data at midnight
        $schedule->command('showcase:cleanup --force')
            ->daily()
            ->at('00:00')
            ->timezone(config('app.timezone', 'UTC'))
            ->withoutOverlapping()
            ->description('Clean up showcase demo data - removes all test orders, customers, and payments');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
