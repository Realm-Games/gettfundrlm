<?php

namespace App\Providers;

use App\Models\Option;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            DB::connection()->getPdo();

            $options = Option::all()->pluck('option_value', 'option_key')->toArray();

            $allOptions = [];
            $allOptions['options'] = $options;
            config($allOptions);

            /**
             * Set dynamic configuration for third party services
             */
            $facebookConfig = [
                'services.facebook' => [
                    'client_id' => get_option('fb_app_id'),
                    'client_secret' => get_option('fb_app_secret'),
                    'redirect' => url('login/facebook-callback'),
                ],
            ];
            $googleConfig = [
                'services.google' => [
                    'client_id' => get_option('google_client_id'),
                    'client_secret' => get_option('google_client_secret'),
                    'redirect' => url('login/google-callback'),
                ],
            ];
            config($facebookConfig);
            config($googleConfig);

            /**
             * Email from name
             */
            $emailConfig = [
                'mail.from' => [
                    'address' => get_option('email_address'),
                    'name' => get_option('site_name'),
                ],
            ];
            config($emailConfig);
        } catch (\Exception $e) {
            if ( 'artisan' !== array_get($_SERVER, 'PHP_SELF') ) {
                echo "<code>{$e->getMessage()}</code>";
                echo "<p>To resolve this issue, you should check your database configuration settings in the <code style='color:#ff7274'>.env</code>  file. Make sure that the database credentials (such as username and password) are correct, and that the database server is running and accessible from your application. If you're still experiencing issues after verifying your configuration, double-check that you have the required database driver installed and configured correctly.</p>";
                exit();
            }
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
