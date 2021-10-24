<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function (Request $request) {

            // Authorization: Basic base64_encode($email:$password)
            if (! $request->hasHeader('Authorization')) {
                return null;
            }

            $header = $request->header('Authorization', '');

            $basic = strrpos($header, 'Basic ') === 0;
            if (! $basic) {
                return null;
            }

            $encoded = substr($header, 6);
            $decoded = base64_decode($encoded);
            if (! stristr($decoded,':')) {
                return null;
            }

            list($email,$password) = explode(':', $decoded);

            $user = User::where('email', $email)->first();
            if (!$user) {
                return null;
            }
            if (Hash::check($password, $user->password)) {
                return $user;
            }
            return null;
        });
    }
}
