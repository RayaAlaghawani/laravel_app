<?php

namespace App\Http\Middleware;

use App\Mail\LoginBlockedMail;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class RoleBasedThrottle
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $email = $request->email ?? 'admin';

        $role = 'admin';

        if ($user) {
            $role = $user->roles->first()->name ?? 'admin';
        }

        $limits = [
            'admin' => 10,
            'employee' => 5,
            'citizen' => 5,
            'guest' => 5,
            // غير المصادقين
        ];

        $maxAttempts = $limits[$role] ?? 3;

        $key = "login:attempts:" . ($user->id ?? $email ?? $request->ip());
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);

            Log::warning("User $email blocked for $seconds seconds due to too many login attempts.");

            // 🔥 التعديل الأول: إرسال الإيميل باستخدام request->email
            // 🔥 التعديل الثاني: إرسال فقط إذا كانت مدة الحظر أكبر من 60 ثانية
            if ($request->email && $seconds > 60) {
                Mail::to($request->email)->send(new LoginBlockedMail($seconds));
            }

            return response()->json([
                'message' => "You have exceeded the allowed number of login attempts. Please wait $seconds seconds before trying again.",
                'retry_after' => $seconds,
                'advice' => "Make sure to enter the correct email and password to avoid being temporarily blocked."
            ], 429);
        }
        RateLimiter::hit($key, 60);

        return $next($request);
    }
}
