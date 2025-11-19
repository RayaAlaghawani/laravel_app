<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthadminRequest;
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
    public function login(AuthadminRequest $request )
    {
            $data = $this->userService->login($request);

            if ($data['code'] === 200) {
                return ResponseHelper::Success($data['user'], $data['message'], $data['code']);
            } else {
                return ResponseHelper::Error($data['user'], $data['message'], $data['code']);
            }}
    /**
     * Store a newly created resource in storage.
     */
    public function logout()
    {
        $data=[];
        try{
            $data=$this->userService->logout( );
            if ($data['code'] === 200) {
                return ResponseHelper::Success([],$data['message'],$data['code']);
            } else {
                return ResponseHelper::Error([], $data['message'], $data['code']);
            }
        } catch (\Throwable $e) {
            return ResponseHelper::Error(null, "Unexpected error: " . $e->getMessage(), 500);
        }}



    public function register(CitizenRegisterRequest $request)
    {
        try {
<<<<<<< HEAD
            $result = $this->userService->register($request->validated());
            return response()->json([
                'message' => 'تم التسجيل بنجاح. يرجى التحقق من بريدك الإلكتروني.',
=======
            // استدعاء Service لتسجيل المستخدم
            $result = $this->userService->register($request->validated());

            // إرجاع JSON response (لا نُرجع رمز التحقق لبيئة الإنتاج، تم إزالته)
            return response()->json([
                'message' => 'تم التسجيل بنجاح. يرجى التحقق من بريدك الإلكتروني.',
                // نُرجع المعرف لمساعدة المستخدم على متابعة التحقق باستخدام المسار الجديد
>>>>>>> 1718eb7ba15695ab7a4044b614f739c7b2f46d69
                'user_id' => $result['user_id'],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'فشل التسجيل. يرجى المحاولة لاحقاً.',
                'error' => $e->getMessage()
            ], 500);
        }
<<<<<<< HEAD
=======
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
>>>>>>> 1718eb7ba15695ab7a4044b614f739c7b2f46d69
    }

    public function verifyEmail(EmailVerificationRequest $request, int $userId)
    {
        $code = $request->input('code');
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
