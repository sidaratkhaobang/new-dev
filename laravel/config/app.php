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

    'timezone' => 'Asia/Bangkok',

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
        Kyslik\ColumnSortable\ColumnSortableServiceProvider::class,
        OwenIt\Auditing\AuditingServiceProvider::class,
        /*
         * Package Service Providers...
         */

        Artisaninweb\SoapWrapper\ServiceProvider::class,
        /* Obs\ObsServiceProvider::class, */
        UndObs\ObsServiceProvider::class,
        Barryvdh\DomPDF\ServiceProvider::class,
        /* Lubianfuchen\DuskDashboard\DuskDashboardServiceProvider::class, */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        // App\Providers\DuskServiceProvider::class,
        Maatwebsite\Excel\ExcelServiceProvider::class,
        Milon\Barcode\BarcodeServiceProvider::class,

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
        'Date' => Illuminate\Support\Facades\Date::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Http' => Illuminate\Support\Facades\Http::class,
        'Js' => Illuminate\Support\Js::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'RateLimiter' => Illuminate\Support\Facades\RateLimiter::class,
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
        'Pusher' => Pusher\Pusher::class,

        'TrafficTicketTrait' => App\Traits\TrafficTicketTrait::class,

        // Enum
        'CarPartTypeEnum' => App\Enums\CarPartTypeEnum::class,
        'PRStatusEnum' => App\Enums\PRStatusEnum::class,
        'DiscountTypeEnum' => App\Enums\DiscountTypeEnum::class,
        'DiscountModeEnum' => App\Enums\DiscountModeEnum::class,
        'DrivingJobStatusEnum' => App\Enums\DrivingJobStatusEnum::class,
        'SpecStatusEnum' => App\Enums\SpecStatusEnum::class,
        'InspectionFormEnum' => App\Enums\InspectionFormEnum::class,
        'ServiceTypeEnum' => App\Enums\ServiceTypeEnum::class,
        'TransferTypeEnum' => App\Enums\TransferTypeEnum::class,
        'InspectionStatusEnum' => App\Enums\InspectionStatusEnum::class,
        'RentalStatusEnum' => App\Enums\RentalStatusEnum::class,
        'DrivingJobTypeStatusEnum' => App\Enums\DrivingJobTypeStatusEnum::class,
        'RentalStateEnum' => App\Enums\RentalStateEnum::class,
        'SelfDriveTypeEnum' => App\Enums\SelfDriveTypeEnum::class,
        'RentalBillTypeEnum' => App\Enums\RentalBillTypeEnum::class,
        'AuctionStatusEnum' => App\Enums\AuctionStatusEnum::class,
        'AuctionResultEnum' => App\Enums\AuctionResultEnum::class,
        'QuotationStatusEnum' => App\Enums\QuotationStatusEnum::class,
        'LongTermRentalStatusEnum' => App\Enums\LongTermRentalStatusEnum::class,
        'LongTermRentalPriceStatusEnum' => App\Enums\LongTermRentalPriceStatusEnum::class,
        'ComparisonPriceStatusEnum' => App\Enums\ComparisonPriceStatusEnum::class,
        'OrderLineTypeEnum' => App\Enums\OrderLineTypeEnum::class,
        'LongTermRentalTypeEnum' => App\Enums\LongTermRentalTypeEnum::class,
        'SAPTransferTypeEnum' => App\Enums\SAPTransferTypeEnum::class,
        'SAPTransferSubTypeEnum' => App\Enums\SAPTransferSubTypeEnum::class,
        'CarStateEnum' => App\Enums\CarStateEnum::class,
        'InspectionTypeEnum' => App\Enums\InspectionTypeEnum::class,
        'TransferReasonEnum' => App\Enums\TransferReasonEnum::class,
        'CustomerTypeEnum' => App\Enums\CustomerTypeEnum::class,
        'LongTermRentalJobType' => App\Enums\LongTermRentalJobType::class,
        'ReceiptStatusEnum' => App\Enums\ReceiptStatusEnum::class,
        'InspectionRemarkEnum' => App\Enums\InspectionRemarkEnum::class,
        'OrderChannelEnum' => App\Enums\OrderChannelEnum::class,
        'RentalTypeEnum' => App\Enums\RentalTypeEnum::class,
        'ConfigApproveTypeEnum' => App\Enums\ConfigApproveTypeEnum::class,
        'InstallEquipmentPOStatusEnum' => App\Enums\InstallEquipmentPOStatusEnum::class,
        'InstallEquipmentStatusEnum' => App\Enums\InstallEquipmentStatusEnum::class,
        'GPSStatusEnum' => App\Enums\GPSStatusEnum::class,
        'GPSJobTypeEnum' => App\Enums\GPSJobTypeEnum::class,
        'GPSStopStatusEnum' => App\Enums\GPSStopStatusEnum::class,
        'GPSHistoricalDataEnum' => App\Enums\GPSHistoricalDataEnum::class,
        'GPSHistoricalDataStatusEnum' => App\Enums\GPSHistoricalDataStatusEnum::class,
        'GPSHistoricalDataTypeEnum' => App\Enums\GPSHistoricalDataTypeEnum::class,
        'TransferCarEnum' => App\Enums\TransferCarEnum::class,
        'BorrowTypeEnum' => App\Enums\BorrowTypeEnum::class,
        'BorrowCarEnum' => App\Enums\BorrowCarEnum::class,
        'ContractSignerSideEnum' => App\Enums\ContractSignerSideEnum::class,
        'ReplacementCarstatusEnum' => App\Enums\ReplacementCarstatusEnum::class,
        'ApproveStatusEnum' => App\Enums\ApproveStatusEnum::class,
        'CarAuctionStatusEnum' => App\Enums\CarAuctionStatusEnum::class,
        'ReplacementJobTypeEnum' => App\Enums\ReplacementJobTypeEnum::class,
        'ContractEnum' => App\Enums\ContractEnum::class,
        'CheckCreditStatusEnum' => App\Enums\CheckCreditStatusEnum::class,
        'RegisterStatusEnum' => App\Enums\RegisterStatusEnum::class,
        'FaceSheetTypeEnum' => App\Enums\FaceSheetTypeEnum::class,

        'PromotionTypeEnum' => App\Enums\PromotionTypeEnum::class,
        'ReplacementCarStatusEnum' => App\Enums\ReplacementCarStatusEnum::class,
        'ReplacementTypeEnum' => App\Enums\ReplacementTypeEnum::class,
        'CheckDistanceTypeEnum' => App\Enums\CheckDistanceTypeEnum::class,
        'RepairEnum' => App\Enums\RepairEnum::class,
        'RepairStatusEnum' => App\Enums\RepairStatusEnum::class,
        'RepairTypeEnum' => App\Enums\RepairTypeEnum::class,
        'RepairBillStatusEnum' => App\Enums\RepairBillStatusEnum::class,
        'LongTermRentalProgressStatusEnum' => App\Enums\LongTermRentalProgressStatusEnum::class,
        'MistakeTypeEnum' => App\Enums\MistakeTypeEnum::class,
        'InsuranceStatusEnum' => App\Enums\InsuranceStatusEnum::class,
        'CMIStatusEnum' => App\Enums\CMIStatusEnum::class,
        'RepairClaimEnum' => App\Enums\RepairClaimEnum::class,
        'RightsEnum' => App\Enums\RightsEnum::class,
        'ImportCarStatusEnum' => App\Enums\ImportCarStatusEnum::class,
        'SellingPriceStatusEnum' => App\Enums\SellingPriceStatusEnum::class,
        'AccidentRepairStatusEnum' => App\Enums\AccidentRepairStatusEnum::class,
        'OfferGMStatusEnum' => App\Enums\OfferGMStatusEnum::class,
        'InsuranceCarStatusEnum' => \App\Enums\InsuranceCarStatusEnum::class,
        'InsuranceCarEnum' => \App\Enums\InsuranceCarEnum::class,
        'FinanceRequestStatusEnum' => \App\Enums\FinanceRequestStatusEnum::class,
        'FinanceContractStatusEnum' => \App\Enums\FinanceContractStatusEnum::class,
        'FinanceStatusEnum' => \App\Enums\FinanceStatusEnum::class,
        'FinanceCarStatusEnum' => \App\Enums\FinanceCarStatusEnum::class,
        'DebtCollectionStatusEnum' => \App\Enums\DebtCollectionStatusEnum::class,
        'DebtCollectionSubStatusEnum' => \App\Enums\DebtCollectionSubStatusEnum::class,
        'DebtCollectionChannelTypeEnum' => \App\Enums\DebtCollectionChannelTypeEnum::class,
        'CheckBillingStatusEnum' => \App\Enums\CheckBillingStatusEnum::class,
        'OwnershipTransferStatusEnum' => App\Enums\OwnershipTransferStatusEnum::class,
        'LitigationLocationEnum' => App\Enums\LitigationLocationEnum::class,
        'LitigationStatusEnum' => App\Enums\LitigationStatusEnum::class,
        'TaxRenewalStatusEnum' => App\Enums\TaxRenewalStatusEnum::class,
        'MaintenanceStatusEnum' => App\Enums\MaintenanceStatusEnum::class,
        'SignYellowTicketStatusEnum' => App\Enums\SignYellowTicketStatusEnum::class,
        'AssetCarTypeEnum' => App\Enums\AssetCarTypeEnum::class,
        'ChangeRegistrationTypeEnum' => App\Enums\ChangeRegistrationTypeEnum::class,
        'ChangeRegistrationRequestTypeContactEnum' => App\Enums\ChangeRegistrationRequestTypeContactEnum::class,
        'ChangeRegistrationStatusEnum' => App\Enums\ChangeRegistrationStatusEnum::class,
        'CompensationStatusEnum' => App\Enums\CompensationStatusEnum::class,
        'MFlowStatusEnum' => App\Enums\MFlowStatusEnum::class,
        'ProgressStepEnum' => App\Enums\ProgressStepEnum::class,
        'NegotiationTypeEnum' => App\Enums\NegotiationTypeEnum::class,
        'OwnershipTransferFaceSheetTypeEnum' => App\Enums\OwnershipTransferFaceSheetTypeEnum::class,
        'RequestReceiptStatusEnum' => App\Enums\RequestReceiptStatusEnum::class,
        'RegisterSignTypeEnum' => App\Enums\RegisterSignTypeEnum::class,
        'TrafficTicketStatusEnum' => App\Enums\TrafficTicketStatusEnum::class,
        'TrafficTicketDocTypeEnum' => App\Enums\TrafficTicketDocTypeEnum::class,
        'CalculateTypeEnum' => App\Enums\CalculateTypeEnum::class,
        
        'PermissionManager' => App\Facades\PermissionManager::class,
        'StepApproveManagement' => App\Facades\StepApproveManagement::class,
        'Actions' => App\Enums\Actions::class,
        'Resources' => App\Enums\Resources::class,
        'Carbon' => Illuminate\Support\Carbon::class,
        'SoapWrapper' => Artisaninweb\SoapWrapper\Facade\SoapWrapper::class,
        'PDF' => Barryvdh\DomPDF\Facade::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
        'DNS1D' => Milon\Barcode\Facades\DNS1DFacade::class,
        'DNS2D' => Milon\Barcode\Facades\DNS2DFacade::class,
        'Carbon' => Illuminate\Support\Carbon::class,
    ],
];
