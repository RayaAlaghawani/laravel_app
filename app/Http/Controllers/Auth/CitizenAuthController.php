<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\CitizenRegisterRequest;
use App\Http\Requests\EmailVerificationRequest;
use App\Services\UserService;
use Illuminate\Http\Request;

class CitizenAuthController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * تسجيل مواطن جديد وإرسال رمز التحقق
     */
    public function register(CitizenRegisterRequest $request)
    {
        try {
            // استدعاء Service لتسجيل المستخدم
            $result = $this->userService->register($request->validated());

            // إرجاع JSON response (لا نُرجع رمز التحقق لبيئة الإنتاج، تم إزالته)
            return response()->json([
                'message' => 'تم التسجيل بنجاح. يرجى التحقق من بريدك الإلكتروني.',
                // نُرجع المعرف لمساعدة المستخدم على متابعة التحقق باستخدام المسار الجديد
                'user_id' => $result['user_id'],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'فشل التسجيل. يرجى المحاولة لاحقاً.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * التحقق من البريد الإلكتروني باستخدام الرمز المرسل
     * @param EmailVerificationRequest $request طلب التحقق (يحتوي فقط على 'code')
     * @param int $userId معرف المستخدم المُمرر في المسار
     */
    // تم التعديل هنا: إضافة $userId كمعامل
    public function verifyEmail(EmailVerificationRequest $request, int $userId)
    {
        // تم التعديل هنا: $userId يأتي من الـ URL وليس من جسم الطلب
        $code = $request->input('code');

        // نرسل الـ userId مباشرة إلى الخدمة
        $user = $this->userService->verifyEmail($userId, $code);

        if (!$user) {
            return response()->json([
                'message' => 'رمز التحقق غير صالح أو انتهت صلاحيته.',
            ], 400);
        }

        return response()->json([
            'message' => 'تم تأكيد البريد الإلكتروني بنجاح.',
            'user' => $user
        ], 200);
    }
}
