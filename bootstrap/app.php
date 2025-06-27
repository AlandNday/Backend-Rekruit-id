<?php
use App\Http\Middleware\ApiTokenMiddleware; // Add this line
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // <--- THIS IS THE CRUCIAL LINE FOR API ROUTES
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api', // <--- This explicitly sets the /api prefix
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Append your custom ApiTokenMiddleware to the 'api' middleware group.
        // This means any route using the 'api' middleware (which is default for routes/api.php)
        // will automatically run your ApiTokenMiddleware.
        $middleware->api(append: [
            ApiTokenMiddleware::class, // <-- Add your custom middleware here
        ]);

        // You can also define aliases here if you want to use a shorter name in routes,
        // e.g., $middleware->alias(['api.token' => ApiTokenMiddleware::class]);
        // However, appending to the 'api' group is often simpler for API-specific middleware.

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();