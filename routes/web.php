<?php

use App\Http\Controllers\LanguageController;
use App\Http\Controllers\VerifyEmailController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Admin\UserAnalyticsController;
use App\Http\Controllers\Admin\TranslationController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\Admin\ChatbotSettingsController;
use App\Http\Controllers\Admin\IconSettingsController;
use App\Http\Controllers\Admin\NavLabelController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Models\Setting;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Email Verification (override Fortify — works without being logged in)
|--------------------------------------------------------------------------
*/
Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

/*
|--------------------------------------------------------------------------
| Social Authentication
| Update redirect URLs in Admin → Social Settings when tunnel URL changes
|--------------------------------------------------------------------------
*/
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
    ->where('provider', 'google|facebook|twitter')
    ->name('social.redirect');

Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->where('provider', 'google|facebook|twitter')
    ->name('social.callback');

Route::get('/auth/telegram/callback', [SocialAuthController::class, 'telegramCallback'])
    ->name('social.telegram.callback');

Route::get('/auth/social/agree', [SocialAuthController::class, 'showAgreement'])
    ->name('social.agree');

Route::post('/auth/social/agree', [SocialAuthController::class, 'processAgreement'])
    ->name('social.agree.store');

Route::post('/auth/social/agree/decline', [SocialAuthController::class, 'declineAgreement'])
    ->name('social.agree.decline');

Route::post('/auth/facebook/deauthorize', [SocialAuthController::class, 'facebookDeauthorize'])
    ->name('social.facebook.deauthorize');

/*
|--------------------------------------------------------------------------
| Landing Page
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('landing');
})->name('landing');

/*
|--------------------------------------------------------------------------
| Terms & Privacy (override Jetstream markdown with DB content)
|--------------------------------------------------------------------------
*/
Route::get('/terms-of-service', function () {
    $settings = Setting::instance();
    $content = $settings->terms_content
        ?: Str::markdown(file_get_contents(resource_path('markdown/terms.md')));
    return view('terms', ['terms' => $content]);
})->name('terms.show');

Route::get('/terms', function () {
    return redirect()->route('terms.show');
})->name('terms.alias');

Route::get('/privacy-policy', function () {
    $settings = Setting::instance();
    $content = $settings->privacy_content
        ?: Str::markdown(file_get_contents(resource_path('markdown/policy.md')));
    return view('policy', ['policy' => $content]);
})->name('policy.show');

Route::get('/privacy', function () {
    return redirect()->route('policy.show');
})->name('privacy.alias');

Route::get('/cookie', function () {
    return redirect()->route('policy.show');
})->name('cookie.alias');

Route::get('/documentation', function () {
    return view('documentation');
})->name('documentation');

/*
|--------------------------------------------------------------------------
| Language Switch
|--------------------------------------------------------------------------
*/
Route::get('/lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');

// AJAX endpoint for verify-email page polling
Route::get('/email/check-verified', function () {
    if (auth()->check() && auth()->user()->hasVerifiedEmail()) {
        return response()->json(['verified' => true]);
    }
    return response()->json(['verified' => false]);
})->middleware(['web', 'auth'])->name('email.check-verified');

/*
|--------------------------------------------------------------------------
| Authenticated + Verified Routes
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    SuperAdminMiddleware::class,
])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('settings');

    Route::get('/settings/icons', [IconSettingsController::class, 'index'])->name('settings.icons');
    Route::post('/settings/icons/{key}', [IconSettingsController::class, 'update'])->name('settings.icons.update');
    Route::get('/settings/nav-labels', [NavLabelController::class, 'index'])->name('settings.nav-labels');
    Route::post('/settings/nav-labels', [NavLabelController::class, 'update'])->name('settings.nav-labels.update');

    Route::get('/users', function () {
        return view('admin.users');
    })->name('users');

    Route::get('/translations', [TranslationController::class, 'index'])->name('translations.index');
    Route::post('/translations/update', [TranslationController::class, 'update'])->name('translations.update');
    Route::delete('/translations/destroy', [TranslationController::class, 'destroy'])->name('translations.destroy');
    Route::post('/translations/auto-translate', [TranslationController::class, 'autoTranslate'])->name('translations.auto-translate');
    Route::post('/translations/auto-translate-all', [TranslationController::class, 'autoTranslateAll'])->name('translations.auto-translate-all');
    Route::post('/translations/scan', [TranslationController::class, 'scanViews'])->name('translations.scan');
    Route::post('/translations/add-scanned', [TranslationController::class, 'addScannedKeys'])->name('translations.add-scanned');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (super_admin + admin)
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    AdminMiddleware::class,
])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/analytics', function () {
        return view('admin.analytics');
    })->name('analytics');

    Route::get('/broadcasts', function () {
        return view('admin.broadcasts');
    })->name('broadcasts');

    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/summary', [UserAnalyticsController::class, 'summary'])->name('summary');
        Route::get('/providers', [UserAnalyticsController::class, 'providerStats'])->name('providers');
        Route::get('/locations', [UserAnalyticsController::class, 'locationStats'])->name('locations');
        Route::get('/devices', [UserAnalyticsController::class, 'deviceStats'])->name('devices');
        Route::get('/browsers', [UserAnalyticsController::class, 'browserStats'])->name('browsers');
        Route::get('/os', [UserAnalyticsController::class, 'osStats'])->name('os');
        Route::get('/registrations/timeline', [UserAnalyticsController::class, 'registrationTimeline'])->name('registrations.timeline');
        Route::get('/logins/timeline', [UserAnalyticsController::class, 'loginTimeline'])->name('logins.timeline');
    });
});

/*
|--------------------------------------------------------------------------
| User Chatbot Routes
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('chatbot')->name('chatbot.')->group(function () {
    Route::get('/', [ChatbotController::class, 'index'])->name('index');
    Route::post('/conversation/new', [ChatbotController::class, 'newConversation'])->name('conversation.new');
    Route::post('/conversation/{id}/send', [ChatbotController::class, 'sendMessage'])->name('conversation.send');
    Route::post('/conversation/{id}/generate-image', [ChatbotController::class, 'generateImage'])->name('conversation.generate-image');
    Route::post('/upload-attachment', [ChatbotController::class, 'uploadAttachment'])->name('upload-attachment');
    Route::get('/conversation/{id}/messages', [ChatbotController::class, 'getMessages'])->name('conversation.messages');
    Route::delete('/conversation/{id}', [ChatbotController::class, 'deleteConversation'])->name('conversation.delete');
    Route::post('/settings', [ChatbotController::class, 'updateUserSettings'])->name('settings.update');
});

/*
|--------------------------------------------------------------------------
| Admin Chatbot Settings Routes
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    SuperAdminMiddleware::class,
])->prefix('admin/settings/chatbot')->name('admin.chatbot.')->group(function () {
    Route::get('/', [ChatbotSettingsController::class, 'index'])->name('index');
    Route::post('/', [ChatbotSettingsController::class, 'update'])->name('update');
    Route::post('/test-api', [ChatbotSettingsController::class, 'testApi'])->name('test-api');
    Route::post('/list-models', [ChatbotSettingsController::class, 'listModels'])->name('list-models');
    Route::get('/documents', [ChatbotSettingsController::class, 'documents'])->name('documents');
    Route::post('/documents', [ChatbotSettingsController::class, 'uploadDocument'])->name('documents.upload');
    Route::delete('/documents/{id}', [ChatbotSettingsController::class, 'deleteDocument'])->name('documents.delete');
    Route::post('/documents/{id}/toggle', [ChatbotSettingsController::class, 'toggleDocument'])->name('documents.toggle');
});
