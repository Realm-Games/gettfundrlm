<?php

use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', ['as' => 'home', 'uses' => 'HomeController@index']);
Route::get('clear', 'HomeController@clearCache')->name('clear_cache');

Auth::routes();
Route::get('/home', 'HomeController@index');

Route::get('campaign/{id}/{slug?}', ['as' => 'campaign_single', 'uses' => 'CampaignsController@show']);
Route::get('campaign-backers/{id}/{slug?}', ['as' => 'campaign_backers', 'uses' => 'CampaignsController@showBackers']);
Route::get('campaign-updates/{id}/{slug?}', ['as' => 'campaign_updates', 'uses' => 'CampaignsController@showUpdates']);
Route::get('campaign-faqs/{id}/{slug?}', ['as' => 'campaign_faqs', 'uses' => 'CampaignsController@showFaqs']);

Route::any('add-to-cart/{reward_id?}', ['as' => 'add_to_cart', 'uses' => 'CampaignsController@addToCart']);
Route::get('contact-us', ['as' => 'contact_us', 'uses' => 'HomeController@contactUs']);
Route::post('contact-us', ['as' => 'contact_us_post', 'uses' => 'HomeController@contactUsPost']);

//categories

Route::get('search', ['as' => 'search', 'uses' => 'CategoriesController@search']);
Route::get('p/{slug}', ['as' => 'single_page', 'uses' => 'PostController@showPage']);

Route::get('categories', ['as' => 'browse_categories', 'uses' => 'CategoriesController@browseCategories']);
Route::get('categories/{id}/{slug?}', ['as' => 'single_category', 'uses' => 'CategoriesController@singleCategory']);

//checkout
Route::get('checkout', ['as' => 'checkout', 'uses' => 'CampaignsController@checkout']);
Route::post('checkout', ['uses' => 'CampaignsController@checkoutPost']);

//Payment
Route::post('checkout/paypal', ['as' => 'payment_paypal_receive', 'uses' => 'CampaignsController@paypalRedirect']);

Route::any('checkout/paypal-success/{transaction_id?}', ['as' => 'payment_success', 'uses' => 'CampaignsController@paymentSuccess']);
Route::any('checkout/paypal-notify/{transaction_id?}', ['as' => 'paypal_notify', 'uses' => 'CampaignsController@paypalNotify']);

Route::post('checkout/stripe', ['as' => 'payment_stripe_receive', 'uses' => 'CampaignsController@paymentStripeReceive']);
Route::post('checkout/bank-transfer', ['as' => 'bank_transfer_submit', 'uses' => 'CampaignsController@paymentBankTransferReceive']);

Route::group(['prefix' => 'ajax'], function () {
    Route::get('new-campaigns', ['as' => 'new_campaigns_ajax', 'uses' => 'HomeController@newCampaignsAjax']);
});

Route::group(['prefix' => 'login'], function () {
    //Social login route
    Route::get('facebook', ['as' => 'facebook_redirect', 'uses' => 'SocialLogin@redirectFacebook']);
    Route::get('facebook-callback', ['as' => 'facebook_callback', 'uses' => 'SocialLogin@callbackFacebook']);

    Route::get('google', ['as' => 'google_redirect', 'uses' => 'SocialLogin@redirectGoogle']);
    Route::get('google-callback', ['as' => 'google_callback', 'uses' => 'SocialLogin@callbackGoogle']);
});

//Dashboard Route
Route::group(['prefix' => 'dashboard', 'middleware' => 'auth'], function () {
    Route::get('/', ['as' => 'dashboard', 'uses' => 'DashboardController@dashboard']);

    Route::group(['prefix' => 'my_campaigns'], function () {
        Route::get('/', ['as' => 'my_campaigns', 'uses' => 'CampaignsController@myCampaigns']);
        Route::get('my_pending_campaigns', ['as' => 'my_pending_campaigns', 'uses' => 'CampaignsController@myPendingCampaigns']);

        Route::get('start_campaign', ['as' => 'start_campaign', 'uses' => 'CampaignsController@create']);
        Route::post('start_campaign', ['uses' => 'CampaignsController@store']);

        Route::get('edit_campaign/{id}', ['as' => 'edit_campaign', 'uses' => 'CampaignsController@edit']);
        Route::post('edit_campaign/{id}', ['uses' => 'CampaignsController@update']);

        //Reward
        Route::get('edit_campaign/{id}/rewards', ['as' => 'edit_campaign_rewards', 'uses' => 'CampaignsController@rewardsInCampaignEdit']);
        Route::post('edit_campaign/{id}/rewards', ['uses' => 'RewardController@store']);

        Route::get('edit_campaign/{id}/rewards/update/{reward_id}', ['as' => 'reward_update', 'uses' => 'RewardController@edit']);
        Route::post('edit_campaign/{id}/rewards/update/{reward_id}', ['uses' => 'RewardController@update']);
        Route::post('delete_reward', ['as' => 'delete_reward', 'uses' => 'RewardController@destroy']);

        //Updates
        Route::get('edit_campaign/{id}/updates', ['as' => 'edit_campaign_updates', 'uses' => 'UpdateController@index']);
        Route::post('edit_campaign/{id}/updates', ['uses' => 'UpdateController@store']);

        Route::get('edit_campaign/{id}/updates/update/{update_id}', ['as' => 'update_update', 'uses' => 'UpdateController@edit']);
        Route::post('edit_campaign/{id}/updates/update/{update_id}', ['uses' => 'UpdateController@update']);
        Route::post('delete_update', ['as' => 'delete_update', 'uses' => 'UpdateController@destroy']);

        //Faq

        Route::get('edit_campaign/{id}/faqs', ['as' => 'edit_campaign_faqs', 'uses' => 'FaqController@index']);
        Route::post('edit_campaign/{id}/faqs', ['uses' => 'FaqController@store']);
        Route::get('edit_campaign/{id}/faqs/update/{faq_id}', ['as' => 'faq_update', 'uses' => 'FaqController@edit']);
        Route::post('edit_campaign/{id}/faqs/update/{faq_id}', ['uses' => 'FaqController@update']);
        Route::post('delete_faq', ['as' => 'delete_faq', 'uses' => 'FaqController@destroy']);

        //Route::get('my_campaigns', ['as'=>'my_campaigns', 'uses' => 'CampaignsController@myCampaigns']);
    });

    /**
     * Restricted area only for admin with middleware->admin
     */
    Route::group(['middleware' => 'admin'], function () {
        Route::group(['prefix' => 'categories'], function () {
            Route::get('/', ['as' => 'categories', 'uses' => 'CategoriesController@index']);
            Route::post('/', ['uses' => 'CategoriesController@store']);
            Route::get('edit/{id}', ['as' => 'edit_categories', 'uses' => 'CategoriesController@edit']);
            Route::post('edit/{id}', ['uses' => 'CategoriesController@update']);
            Route::post('delete-categories', ['as' => 'delete_categories', 'uses' => 'CategoriesController@destroy']);
        });

        Route::group(['prefix' => 'campaigns'], function () {
            Route::get('all_campaigns', ['as' => 'all_campaigns', 'uses' => 'CampaignsController@allCampaigns']);
            Route::get('staff_picks', ['as' => 'staff_picks', 'uses' => 'CampaignsController@staffPicksCampaigns']);
            Route::get('funded', ['as' => 'funded', 'uses' => 'CampaignsController@fundedCampaigns']);
            Route::get('blocked_campaigns', ['as' => 'blocked_campaigns', 'uses' => 'CampaignsController@blockedCampaigns']);
            Route::get('pending_campaigns', ['as' => 'pending_campaigns', 'uses' => 'CampaignsController@pendingCampaigns']);

            Route::get('expired_campaigns', ['as' => 'expired_campaigns', 'uses' => 'CampaignsController@expiredCampaigns']);
            Route::get('campaign-search', ['as' => 'campaign_admin_search', 'uses' => 'CampaignsController@searchAdminCampaigns']);

            Route::get('campaign_status/{id}/{status}', ['as' => 'campaign_status', 'uses' => 'CampaignsController@statusChange']);

            Route::get('campaign_delete/{id}', ['as' => 'campaign_delete', 'uses' => 'CampaignsController@deleteCampaigns']);
        });

        //Settings
        Route::group(['prefix' => 'settings'], function () {
            Route::get('theme-settings', ['as' => 'theme_settings', 'uses' => 'SettingsController@ThemeSettings']);
            Route::get('general', ['as' => 'general_settings', 'uses' => 'SettingsController@GeneralSettings']);
            Route::get('payments', ['as' => 'payment_settings', 'uses' => 'SettingsController@PaymentSettings']);

            Route::get('social', ['as' => 'social_settings', 'uses' => 'SettingsController@SocialSettings']);
            Route::get('recaptcha', ['as' => 're_captcha_settings', 'uses' => 'SettingsController@reCaptchaSettings']);

            //Save settings / options
            Route::post('save-settings', ['as' => 'save_settings', 'uses' => 'SettingsController@update']);

            Route::get('other', ['as' => 'other_settings', 'uses' => 'SettingsController@OtherSettings']);
            Route::post('other', [SettingsController::class, 'OtherSettingsPost']);
        });

        Route::group(['prefix' => 'pages'], function () {
            Route::get('/', ['as' => 'pages', 'uses' => 'PostController@index']);

            Route::get('create', ['as' => 'create_new_page', 'uses' => 'PostController@create']);
            Route::post('create', ['uses' => 'PostController@store']);
            Route::post('delete', ['as' => 'delete_page', 'uses' => 'PostController@destroy']);

            Route::get('edit/{slug}', ['as' => 'edit_page', 'uses' => 'PostController@edit']);
            Route::post('edit/{slug}', ['uses' => 'PostController@updatePage']);
        });

        Route::group(['prefix' => 'users'], function () {
            Route::get('/', ['as' => 'users', 'uses' => 'UserController@index']);
            Route::get('view/{slug}', ['as' => 'users_view', 'uses' => 'UserController@show']);
            Route::get('user_status/{id}/{status}', ['as' => 'user_status', 'uses' => 'UserController@statusChange']);

            //Edit
            Route::get('edit/{id}', ['as' => 'users_edit', 'uses' => 'UserController@profileEdit']);
            Route::post('edit/{id}', ['uses' => 'UserController@profileEditPost']);
            Route::get('profile/change-avatar/{id}', ['as' => 'change_avatar', 'uses' => 'UserController@changeAvatar']);
            //Route::post('upload-avatar/{id}', ['as'=>'upload_avatar',  'uses' => 'UserController@uploadAvatar']);
        });

        Route::group(['prefix' => 'withdrawal-requests'], function () {
            Route::get('/', ['as' => 'withdrawal_requests', 'uses' => 'PaymentController@withdrawalRequests']);
        });
    });

    Route::group(['prefix' => 'payments'], function () {
        Route::get('/', ['as' => 'payments', 'uses' => 'PaymentController@index']);
        Route::get('pending', ['as' => 'payments_pending', 'uses' => 'PaymentController@paymentsPending']);
        Route::get('view/{id}', ['as' => 'payment_view', 'uses' => 'PaymentController@view']);
        Route::get('status-change/{id}/{status}', ['as' => 'status_change', 'uses' => 'PaymentController@markSuccess']);
        Route::get('delete/{id}', ['as' => 'payment_delete', 'uses' => 'PaymentController@delete']);
    });

    Route::group(['prefix' => 'withdraw'], function () {
        Route::get('/', ['as' => 'withdraw', 'uses' => 'PaymentController@withdraw']);
        Route::post('/', ['uses' => 'PaymentController@withdrawRequest']);

        Route::get('view/{id}', ['as' => 'withdraw_request_view', 'uses' => 'PaymentController@withdrawRequestView']);
        Route::post('view/{id}', ['uses' => 'PaymentController@withdrawalRequestsStatusSwitch']);
    });

    Route::group(['prefix' => 'u'], function () {
        Route::get('profile', ['as' => 'profile', 'uses' => 'UserController@profile']);
        Route::get('profile/edit', ['as' => 'profile_edit', 'uses' => 'UserController@profileEdit']);
        Route::post('profile/edit', ['uses' => 'UserController@profileEditPost']);
        Route::get('profile/change-avatar', [UserController::class, 'changeAvatar']);
        Route::post('upload-avatar', ['as' => 'upload_avatar',  'uses' => 'UserController@uploadAvatar']);

        //Withdrawals
        Route::get('withdrawal-preference', ['as' => 'withdrawal_preference',  'uses' => 'UserController@withdrawalPreference']);
        Route::post('withdrawal-preference', ['uses' => 'UserController@withdrawalPreferenceUpdate']);

        /**
         * Change Password route
         */
        Route::group(['prefix' => 'account'], function () {
            Route::get('change-password', ['as' => 'change_password', 'uses' => 'UserController@changePassword']);
            Route::post('change-password', 'UserController@changePasswordPost');
        });
    });
});
