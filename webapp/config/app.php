<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    */

    'name' => env('APP_NAME', 'Barbapappa'),

    /**
     * Application version name and code.
     */
    'version_name' => '0.1.181',
    'version_code' => 181,

    'author' => 'Tim Visée',
    'description' => 'Bar management application to manage transactions and inventory',
    'keywords' => 'bar, barbapappa, barapp, management, payment, community',

    /**
     * Application source location.
     */
    'source' => 'https://gitlab.com/timvisee/barbapappa',
    'source_version_page' => 'https://gitlab.com/timvisee/barbapappa/-/tags/v{}',

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services your application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => env('APP_DEBUG', false),

    // Do not leak sensitive details through debug output
    'debug_blacklist' => [
        '_ENV' => [
            'APP_KEY',
            'DB_PASSWORD',
            'REDIS_PASSWORD',
            'MAIL_PASSWORD',
            'AWS_APP_SECRET',
            'OPENEXCHANGERATES_API_KEY',
            'PUSHER_APP_KEY',
            'PUSHER_APP_SECRET',
            'S3_BUCKET_SECRET',
            'SENTRY_LARAVEL_DSN',
            'SOCKET_APP_SECRET',
            'TWILIO_APP_SECRET',
        ],
        '_SERVER' => [
            'APP_KEY',
            'DB_PASSWORD',
            'REDIS_PASSWORD',
            'MAIL_PASSWORD',
            'AWS_APP_SECRET',
            'OPENEXCHANGERATES_API_KEY',
            'PUSHER_APP_KEY',
            'PUSHER_APP_SECRET',
            'S3_BUCKET_SECRET',
            'SENTRY_LARAVEL_DSN',
            'SOCKET_APP_SECRET',
            'TWILIO_APP_SECRET',
        ],
        '_POST' => [
            'password',
            'password_confirmation',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',

    /**
     * Available locales.
     * This should also include the hidden locales.
     */
    'locales' => [
        'en',
        'nl',
        'pirate',
    ],

    /**
     * The locales that are hidden by default.
     */
    'hidden_locales' => [
        'pirate',
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Pirate easter egg
    |--------------------------------------------------------------------------
    |
    | Chance of the pirate easter egg to appear in the sidebar per page load.
    | The chance is a factor:
    | - 1.0  = 100%
    | - 0.1  = 10%
    | - 0.01 = 1%
    |
    */

    'pirate_chance' => 0.005,

    /*
    |--------------------------------------------------------------------------
    | Session link authentication
    |--------------------------------------------------------------------------
    |
    | Enable or disable authentication with session links.
    | If enabled, users with authenticate with a session link through email by
    | default. An optional password can be set on the users profile page.
    | If disabled, a user must specify a password on registration.
    |
    */

    'auth_session_link' => true,

    // Time for authentication session links to expire in seconds
    'auth_session_link_expire' => 30 * 60,

    // Time for authentication session link codes to expire in seconds
    'auth_session_link_code_expire' => 10 * 60,

    /*
    |--------------------------------------------------------------------------
    | Application specific email configuration.
    |--------------------------------------------------------------------------
    */

    // Email address limit per account
    'email_limit' => 5,

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'nl',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */
        Laravel\Tinker\TinkerServiceProvider::class,

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

        /**
         * Laravel Collective HTML providers...
         */
        Collective\Html\HtmlServiceProvider::class,

        /**
         * Laravel DebugBar.
         */
        Barryvdh\Debugbar\ServiceProvider::class,

        /**
         * Laravel DomPDF.
         */
        Barryvdh\DomPDF\ServiceProvider::class,

        /**
         * Sentry.
         */
        Sentry\Laravel\ServiceProvider::class,

        /**
         * Eloquent HasMany Sync.
         */
        Alfa6661\EloquentHasManySync\ServiceProvider::class,

        /**
         * Laravel Excel.
         */
        Maatwebsite\Excel\ExcelServiceProvider::class,

        /**
         * Laravel to JavaScript localization.
         */
        Mariuzzo\LaravelJsLocalization\LaravelJsLocalizationServiceProvider::class,

        /**
         * Barbapappa service providers.
         */
        App\Providers\LanguageManagerServiceProvider::class,
        App\Providers\BarAuthServiceProvider::class,
        App\Providers\KioskAuthServiceProvider::class,
        App\Providers\LogoServiceProvider::class,
        App\Providers\PermissionManagerServiceProvider::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App' => Illuminate\Support\Facades\App::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,

        /**
         * Laravel Collective HTML aliases...
         */
        'Form' => Collective\Html\FormFacade::class,
        'Html' => Collective\Html\HtmlFacade::class,

        /**
         * Laravel DebugBar.
         */
        'Debugbar' => Barryvdh\Debugbar\Facade::class,

        /**
         * Laravel DomPDF.
         */
        'PDF' => Barryvdh\DomPDF\Facade::class,

        /**
         * Sentry
         */
        'Sentry' => Sentry\Laravel\Facade::class,

        /**
         * Barbapappa aliases.
         */
        'ErrorRenderer' => App\Helpers\ErrorRenderer::class,
    ],

];
