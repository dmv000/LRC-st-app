<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\PrivateFunct as PF;
use Auth;

use App\Http\ORM\Event;
use App\Http\ORM\EventType;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';
    protected $loginPath = '/login';

    /**
     * The login form field that identifies the user (used for throttling).
     */
    public function username()
    {
        return 'username';
    }

    protected function Login(Request $request) {
        
        $username = trim((string) $request->input('username'));
        $password = (string) $request->input('password');

        if($username === '' || $password === '') {
            return redirect($this->loginPath)
                    ->withErrors([
                        'password' => 'Wrong Credentials',
                    ]);
        }

        // Lock out after too many failed attempts (5 per minute per user + IP)
        if($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

$result = PF::authenticate($username, $password);

        $user = null;
        if($result == 1) {

            // the user exists in the LDAP server, with the provided password

            PF::sync_users();
            $user = \App\User::where('nickname', $username) -> first();

            if($user == null) {
                Log::warning('User ' . $username . ' authenticated on LDAP but was not found after sync');
            }
        }

        if($user != null) {

            $this->clearLoginAttempts($request);
            $this->guard()->login($user);
	    //Log::debug('User ' . Auth::user()->nickname . ' logged in');

$event = new Event;
$event->user_id = Auth::user()->id;
$event->event_type = EventType::where('description','Log In')->first()->id;
$event->save();

            return redirect($this->redirectPath());
        }

        $this->incrementLoginAttempts($request);

        return redirect($this->loginPath)
                ->withErrors([
                    'password' => 'Wrong Credentials',
                ]);
    }
}
