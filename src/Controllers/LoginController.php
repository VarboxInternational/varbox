<?php

namespace Varbox\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LoginController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, AuthenticatesUsers;

    /**
     * Get the maximum number of attempts to allow.
     *
     * @return int
     */
    protected $maxAttempts = 3;

    /**
     * Get the number of minutes to throttle for.
     *
     * @return int
     */
    protected $decayMinutes = 1;

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function show()
    {
        meta()->set('title', 'Admin');

        return view('varbox::admin.auth.login');
    }

    /**
     * The user has been authenticated.
     *
     * @param Request $request
     * @param Authenticatable $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, $user)
    {
        if (!($user->isActive() && ($user->hasRole('Admin') || $user->hasRole('Super')))) {
            $this->guard()->logout();

            $request->session()->invalidate();

            return back()->withErrors([
                'These credentials do not match our records. '
            ]);
        }

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return auth()->guard('admin');
    }

    /**
     * Where to redirect users after login.
     *
     * @return string
     */
    protected function redirectTo()
    {
        return route('admin');
    }
}
