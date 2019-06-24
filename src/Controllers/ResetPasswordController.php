<?php

namespace Varbox\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ResetPasswordController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ResetsPasswords;

    /**
     * Show the application's reset password form.
     *
     * @param Request $request
     * @param null $token
     * @return $this
     */
    public function show(Request $request, $token = null)
    {
        meta()->set('title', 'Admin - Reset Password');

        return view('varbox::admin.auth.password_reset')->with([
            'email' => $request->email,
            'token' => $token,
        ]);
    }

    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectTo()
    {
        return route('admin.login');
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param \Illuminate\Http\Request  $request
     * @param string $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
        return redirect($this->redirectPath())->with([
            'message' => 'Your password has been reset. You can now sign in using your new password.'
        ]);
    }
}
