<?php

namespace Varbox\Exceptions;

use Illuminate\Auth\AuthenticationException as BaseAuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AuthenticationException extends BaseAuthenticationException
{
    /**
     * Create a new authentication exception.
     *
     * @param  string  $message
     * @param  array  $guards
     * @return void
     */
    public function __construct($message = 'Unauthenticated.', array $guards = [])
    {
        parent::__construct($message, $guards);
    }

    /**
     * Render the exception.
     *
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => $this->getMessage()
            ], 401);
        }

        if (in_array('admin', $this->guards())) {
            return redirect()->route('admin.login');
        }

        throw new parent($this->getMessage(), $this->guards());
    }
}
