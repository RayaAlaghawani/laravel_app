<?php

namespace App\Services;

use App\Mail\EmailVerificationMail;
use App\Models\EmailVerification;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserService
{
    /**
     * تسجيل مواطن جديد وتوليد رمز التحقق.
     */
    public function register(array $data)
    {
        DB::beginTransaction();
        try {
            // 1. إنشاء المستخدم
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'national_id' => $data['national_id'],
                'password' => Hash::make($data['password']), // استخدام Hash::make أفضل من bcrypt
            ]);

            $user->assignRole('citizen');

            // 2. توليد وحفظ رمز التحقق (OTP)
            $verificationCode = random_int(100000, 999999);

            EmailVerification::create([
                'user_id' => $user->id,
                'code' => (string) $verificationCode,
                'expires_at' => now()->addMinutes(10), // رمز التحقق صالح لمدة 10 دقائق
            ]);

            // 3. إرسال الإيميل (يجب أن يكون لديك الكلاس App\Mail\EmailVerificationMail جاهزاً)
            Mail::to($user->email)->send(new EmailVerificationMail($verificationCode));

            DB::commit();

            return [
                'user_id' => $user->id,
                'message' => 'Registration successful. Verification code sent to email.'
            ];

        } catch (Exception $e) {
            DB::rollBack();
            // يمكنك تسجيل الخطأ هنا
            throw new Exception('Registration failed: ' . $e->getMessage());
        }
    }

    /**
     * التحقق من البريد الإلكتروني باستخدام الرمز (OTP).
     * @param int $userId معرف المستخدم الذي يحاول التحقق
     * @param string $code رمز التحقق المرسل
     * @return User|null المستخدم بعد التحقق
     */
    public function verifyEmail(int $userId, string $code)
    {
        DB::beginTransaction();
        try {
            // 1. البحث عن رمز التحقق النشط المرتبط بهذا المستخدم
            $verification = EmailVerification::where('user_id', $userId)
                ->where('code', $code)
                ->where('expires_at', '>', now())
                ->first();

            if (!$verification) {
                DB::rollBack();
                return null; // رمز غير صالح أو منتهي الصلاحية
            }

            // 2. تحديث المستخدم كـ verified
            $user = $verification->user;
            $user->email_verified_at = now();
            $user->save();

            // 3. حذف رمز التحقق
            $verification->delete();

            DB::commit();
            return $user->load('roles');

        } catch (Exception $e) {
            DB::rollBack();
            // يمكنك تسجيل الخطأ هنا
            throw new Exception('Email verification failed.');
        }
    }
}
