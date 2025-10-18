<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\EmailConfirmation;

class EmailConfirmationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply to confirmation routes
        if ($request->routeIs('email.confirm', 'password.confirm')) {
            $token = $request->route('token');
            
            if (!$token) {
                return redirect()->route('settings')->with('error', 'Invalid confirmation link.');
            }

            // Check if token exists and is valid
            $confirmation = EmailConfirmation::where('token', $token)->first();
            
            if (!$confirmation) {
                return redirect()->route('settings')->with('error', 'Invalid confirmation link.');
            }

            if (!$confirmation->isValid()) {
                return redirect()->route('settings')->with('error', 'Confirmation link has expired. Please request a new one.');
            }

            // Add confirmation to request for use in controllers
            $request->merge(['email_confirmation' => $confirmation]);
        }

        return $next($request);
    }
}
