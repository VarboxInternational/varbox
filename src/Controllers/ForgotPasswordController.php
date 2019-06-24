<?php

namespace Varbox\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ForgotPasswordController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, SendsPasswordResetEmails;

    /**
     * Show the application's forgot password form.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        meta()->set('title', 'Admin - Forgot Password');

        return view('varbox::admin.auth.password_forgot');
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return redirect()->route('admin.login')->with([
            'message' => 'A reset link for your password has been sent to your email address.'
        ]);
    }
}
