<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Notifications\TwoFactorCodeNotification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
            public function store(LoginRequest $request)

            {

                $credentials = $request->only('email', 'password');

                $user = User::where('email', $credentials['email'])->first();
                
                if (!isset($user) || !password_verify($credentials['password'], $user->password)) {
                    return response()->json(['message' => 'Invalid credentials'], 403);
                }

                    $code = rand(10000, 99999);

                    $user->notify(new TwoFactorCodeNotification($code));
            
                    $request->session()->put('authenticated_user_id', $user->id);
                    $request->session()->put('two_factor_code', $code);

                    $request->session()->regenerate();  

                    return response()->json(['message' => "An OTP code was send to $request->email", 'email' => $request->email], 200);
                
                // $request->authenticate();
            }

            public function verify(Request $request) {
                $userCode = $request->input('code');
                $storedCode = $request->session()->get('two_factor_code');

                if ($userCode == $storedCode) {

                    $authenticatedUserId = $request->session()->get('authenticated_user_id');
                    $user = User::find($authenticatedUserId);
                    
                    if ($user) {
                        Auth::login($user);

                        // Remove the 2FA code and authenticated user ID from the session
                        $request->session()->forget('two_factor_code');
                        $request->session()->forget('authenticated_user_id');
                        return response()->json(['message' => 'all find'], 200);
                    }


                }

                return response()->json(['message' => 'Provided code is incorrect!'], 401);
            }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
