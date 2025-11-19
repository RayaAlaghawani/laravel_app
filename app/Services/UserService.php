<?php

namespace App\Services;

use App\DTOs\LoginDTO;
use App\Http\Requests\AuthadminRequest;
use App\Mail\EmailVerificationMail;
use App\Models\EmailVerification;
use App\Models\User;
use App\Repositories\userRepository;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Mockery\Exception;

class UserService
{
    protected $repository;

    public function __construct(userRepository $repository)
    {
        $this->repository = $repository;
    }
    public function login(AuthadminRequest $request)
    {
        $email = $request->email;
        $key = 'login-attempts:' . $email;

        $user = $this->repository->getByEmail($email);
if($user)
        if (!$user || !Auth::attempt($request->only(['email', 'password']))) {
            return [
                'user' => null,
                'message' => 'Invalid credentials',
                'code' => 401
            ];
        }
        if (is_null($user->email_verified_at)) {
            return [
                'user' => null,
                'message' => 'البريد الإلكتروني غير مُفعل. يرجى إتمام عملية التحقق.',
                'code' => 403
            ];
        }

        $key = "login:attempts:" . ($user->id ?? $email);
        RateLimiter::clear($key);

        $user = $this->appendRolesAndPermission($user);

        $user['token'] = $user->createToken('token')->plainTextToken;

        return [
            'user' => $user,
            'message' => 'Login successful',
            'code' => 200
        ];
    }
    public function logout(){
        $user=Auth::user();
        if(! is_null($user)){
            $user->delete();
            $message='user Logged out  Successfully';
            $code=200;}
        else{
            $message='invaild token';
            $code=404;}
        return (['user' => $user, 'message' => $message, 'code' => $code]);
    }
    private function appendRolesAndPermission($user)
    {

        $roles = [];
        foreach ($user->roles as $role) {
            $roles[] = $role->name;

        }
        unset($user['roles']);
        $user['roles'] = $roles;
        $permissions = [];
        foreach ($user->permissions as $permission) {
            $permissions[] = $permission->name;
        }
        unset($user['permissions']);
        $user['permissions'] = $permissions;
        return $user;
    }
    /////////////////////
    public function register(array $data)
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'national_id' => $data['national_id'],
                'password' => Hash::make($data['password']),
            ]);

            $employeeRole = $this->repository->getByName('citizen');
            $user->assignRole($employeeRole);
            if ($user->has('permissions')) {
                $user->syncPermissions($user->permissions);
            }
            $user->load('permissions', 'roles');
            $user = $this->repository->getById($user->id);
            $user = $this->appendRolesAndPermission($user);

            $verificationCode = random_int(100000, 999999);

            EmailVerification::create([
                'user_id' => $user->id,
                'code' => (string) $verificationCode,
                'expires_at' => now()->addMinutes(10),
            ]);

            Mail::to($user->email)->send(new EmailVerificationMail($verificationCode));

            DB::commit();

            return [
                'user_id' => $user->id,
                'message' => 'Registration successful. Verification code sent to email.'
            ];

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Registration failed: ' . $e->getMessage());
        }
    }

    public function verifyEmail(int $userId, string $code)
    {
        DB::beginTransaction();
        try {
            $verification = EmailVerification::where('user_id', $userId)
                ->where('code', $code)
                ->where('expires_at', '>', now())
                ->first();

            if (!$verification) {
                DB::rollBack();
                return null;
            }

            $user = $verification->user;
            $user->email_verified_at = now();
            $user->save();

            $verification->delete();

            DB::commit();
            return $user->load('roles');

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception('Email verification failed.');
        }
    }

}
