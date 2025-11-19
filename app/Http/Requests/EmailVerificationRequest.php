<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // تم حذف التحقق من 'user_id' لأنه أصبح يأتي من المسار
        return [
<<<<<<< HEAD
          //  'email' => 'required|email|exists:users,email',
=======
>>>>>>> 1718eb7ba15695ab7a4044b614f739c7b2f46d69
            'code'  => 'required|digits:6',
        ];
    }
}
