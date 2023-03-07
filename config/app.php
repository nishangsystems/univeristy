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
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
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

    'debug' => (bool)env('APP_DEBUG', false),

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

    'asset_url' => env('ASSET_URL', null),

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

    'timezone' => 'Africa/Douala',

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
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

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

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\PermissionsServiceProvider::class,
        Barryvdh\DomPDF\ServiceProvider::class,
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
        'Arr' => Illuminate\Support\Arr::class,
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
        'Http' => Illuminate\Support\Facades\Http::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        // 'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'Str' => Illuminate\Support\Str::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,
        'PDF' => Barryvdh\DomPDF\Facade::class,
    ],

    /*
|--------------------------------------------------------------------------
| MOMO API Environment VARIABLES
|--------------------------------------------------------------------------
|
| This value determines the "environment"
|
*/

    'mtn-momo' => [
        // Momo API transaction currency code.
        'currency' => env('MOMO_CURRENCY', 'EUR'),

        /*
         * Target environment.
         *
         * Also called; targetEnvironment
         */
        'environment' => env('MOMO_ENVIRONMENT', 'sandbox'),

        /*
         * Product.
         *
         * The product you subscribed too.
         *
         * @see https://momodeveloper.mtn.com/products
         */
        'product' => env('MOMO_PRODUCT', 'collection'),

        /*
         * Provider Callback Host.
         *
         * It's basically the host for your server domain.
         */
        'provider_callback_host' => env('MOMO_PROVIDER_CALLBACK_HOST', 'localhost'),

        'api' => [
            // API base URI.
            'base_uri' => env('MOMO_API_BASE_URI', 'https://ericssonbasicapi1.azure-api.net/'),

            // Register client ID URI
            'register_id_uri' => env('MOMO_API_REGISTER_ID_URI', 'v1_0/apiuser'),

            // Validate client ID URI
            'validate_id_uri' => env('MOMO_API_VALIDATE_ID_URI', 'v1_0/apiuser/{clientId}'),

            // Generate client secret URI
            'request_secret_uri' => env('MOMO_API_REQUEST_SECRET_URI', 'v1_0/apiuser/{clientId}/apikey'),
        ],

        'products' => [
            'collection' => [
                /*
                 * Client app ID.
                 *
                 * Also called; X-Reference-Id and api_user_id interchangeably
                 *
                 * User generated UUID4 string, and it has to be registered with the API.
                 */
                'id' => env('MOMO_COLLECTION_ID'),

                /*
                 * Callback URI.
                 *
                 * Also called; providerCallbackHost
                 */
                'callback_uri' => env('MOMO_COLLECTION_CALLBACK_URI'),

                /*
                 * Client app secret.
                 *
                 * Also called; apiKey
                 *
                 * Requested for secret from the MTN Momo API.
                 */
                'secret' => env('MOMO_COLLECTION_SECRET'),

                /*
                 * Production subscription key.
                 *
                 * Also called; Ocp-Apim-Subscription-Key
                 */
                'key' => env('MOMO_COLLECTION_SUBSCRIPTION_KEY'),

                // Party ID type
                'party_id_type' => env('MOMO_COLLECTION_PARTY_ID_TYPE', 'msisdn'),

                // Token uri
                'token_uri' => env('MOMO_COLLECTION_TOKEN_URI', 'collection/token/'),

                // Transact (collect)
                'transaction_uri' => env('MOMO_COLLECTION_TRANSACTION_URI', 'collection/v1_0/requesttopay'),

                // Transaction status
                'transaction_status_uri' => env(
                    'MOMO_COLLECTION_TRANSACTION_STATUS_URI',
                    'collection/v1_0/requesttopay/{momoTransactionId}'
                ),

                // Account balance
                'account_balance_uri' => env('MOMO_COLLECTION_APP_BALANCE_URI', 'collection/v1_0/account/balance'),

                // Account status
                'account_status_uri' => env(
                    'MOMO_COLLECTION_USER_ACCOUNT_URI',
                    'collection/v1_0/accountholder/{partyIdType}/{partyId}/active'
                ),

                // Account Holder Info
                'account_holder_info_uri' => env(
                    'MOMO_COLLECTION_USER_ACCOUNT_HOLDER_INFO_URI',
                    'collection/v1_0/accountholder/msisdn/{partyId}/basicuserinfo'
                ),
            ],
            'disbursement' => [
                'id' => env('MOMO_DISBURSEMENT_ID'),

                'callback_uri' => env('MOMO_DISBURSEMENT_CALLBACK_URI'),

                'secret' => env('MOMO_DISBURSEMENT_SECRET'),

                'key' => env('MOMO_DISBURSEMENT_SUBSCRIPTION_KEY'),

                'party_id_type' => env('MOMO_DISBURSEMENT_PARTY_ID_TYPE', 'msisdn'),

                // Token uri
                'token_uri' => env('MOMO_DISBURSEMENT_TOKEN_URI', 'disbursement/token/'),

                // Transact (disburse)
                'transaction_uri' => env('MOMO_DISBURSEMENT_TRANSACTION_URI', 'disbursement/v1_0/transfer'),

                // Transaction status
                'transaction_status_uri' => env(
                    'MOMO_DISBURSEMENT_TRANSACTION_STATUS_URI',
                    'disbursement/v1_0/transfer/{momoTransactionId}'
                ),

                // Account balance
                'account_balance_uri' => env('MOMO_DISBURSEMENT_APP_BALANCE_URI', 'disbursement/v1_0/account/balance'),

                // Account status
                'account_status_uri' => env(
                    'MOMO_DISBURSEMENT_USER_ACCOUNT_URI',
                    'disbursement/v1_0/accountholder/{partyIdType}/{partyId}/active'
                ),

                // Account Holder Info
                'account_holder_info_uri' => env(
                    'MOMO_DISBURSEMENT_USER_ACCOUNT_HOLDER_INFO_URI',
                    'disbursement/v1_0/accountholder/msisdn/{partyId}/basicuserinfo'
                ),
            ],
            'remittance' => [
                'id' => env('MOMO_REMITTANCE_ID'),

                'callback_uri' => env('MOMO_REMITTANCE_CALLBACK_URI'),

                'secret' => env('MOMO_REMITTANCE_SECRET'),

                'key' => env('MOMO_REMITTANCE_SUBSCRIPTION_KEY'),

                'party_id_type' => env('MOMO_REMITTANCE_PARTY_ID_TYPE', 'msisdn'),

                // Token uri
                'token_uri' => env('MOMO_REMITTANCE_TOKEN_URI', 'remittance/token/'),

                // Transact (remit)
                'transaction_uri' => env('MOMO_REMITTANCE_TRANSACTION_URI', 'remittance/v1_0/transfer'),

                // Transaction status
                'transaction_status_uri' => env(
                    'MOMO_REMITTANCE_TRANSACTION_STATUS_URI',
                    'remittance/v1_0/transfer/{momoTransactionId}'
                ),

                // Account balance
                'account_balance_uri' => env('MOMO_REMITTANCE_APP_BALANCE_URI', 'remittance/v1_0/account/balance'),

                // Account status
                'account_status_uri' => env(
                    'MOMO_REMITTANCE_USER_ACCOUNT_URI',
                    'remittance/v1_0/accountholder/{partyIdType}/{partyId}/active'
                ),

                // Account Holder Info
                'account_holder_info_uri' => env(
                    'MOMO_REMITTANCE_USER_ACCOUNT_HOLDER_INFO_URI',
                    'remittance/v1_0/accountholder/msisdn/{partyId}/basicuserinfo'
                ),
            ],
        ],
        /*
         * GuzzleHttp client request options.
         * http://docs.guzzlephp.org/en/stable/request-options.html
         */
        'guzzle' => [
            'options' => [
                // 'verify' => false,
            ],
        ],
    ],


];
