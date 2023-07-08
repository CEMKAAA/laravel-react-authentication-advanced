<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerification;
use App\Models\Users;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{

    public function sendMail($email, $token)
    {

        Mail::to($email)->send(new EmailVerification($token));

    }

    public function register(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'mobile' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*[!.,])[a-zA-Z!.,0-9]+$/'
                ],
            ], [
                'name.required' => 'The name field is required.',
                'name.string' => 'The name must be a string.',
                'name.max' => 'The name may not be greater than :max characters.',
                'email.required' => 'The email field is required.',
                'mobile.required' => 'The mobile field is required.',
                'mobile.string' => 'The mobile must be a string.',
                'mobile.regex' => 'The mobile must be valid.',
                'email.string' => 'The email must be a string.',
                'email.email' => 'The email must be a valid email address.',
                'email.max' => 'The email may not be greater than :max characters.',
                'email.unique' => 'The email has already been taken.',
                'password.required' => 'The password field is required.',
                'password.string' => 'The password must be a string.',
                'password.min' => 'The password must be at least :min characters.',
                'password.confirmed' => 'The password confirmation does not match.',
                'password.regex' => 'The password must contain at least one uppercase letter, one special character [!,.], and one number.',
            ]);

            $errors = [];

            if ($validator->fails())
            {
                $errors = $validator->errors()->all();
                return response(['errors' => $errors], 422);
            }

            if ($request->password != $request->confirmPassword)
            {
                return response(['errors' => ['sifreler birbirine uymuyor']]);
            }

            $verificationToken = Str::random(60);
            $expirationTime = Carbon::now()->addMinutes(5);

            $this->sendMail($request->email, $verificationToken);

            $user = Users::create([
                'name' => $request->name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'verificationToken' => $verificationToken,
                'verification_token_expires_at' => $expirationTime,
                'status' => 1
            ]);

            if (!$user)
            {
                return response(['errors' => ['An error occurred while creating the user.']], 500);
            }

            return response(['message' => 'email has been sent', 'email' => $user->email, 'expireDate' => $expirationTime]);
        }
        catch (\Exception $e)
        {
            return response(['errors' => [$e->getMessage()]], 500);
        }
    }

    public function sendAgain(Request $request)
    {
        $email = $request->data;
        $verificationToken = Str::random(60);
        $expireDate = Carbon::now()->addMinute(5);

        $user = Users::where('email', $email)->first();

        if (!$user) {
            return response(['error' => 'Kullanıcı bulunamadı']);
        }

        $user->verificationToken = $verificationToken;
        $user->verification_token_expires_at = $expireDate;

        if (!$user->save()) {
            return response(['error' => 'Kullanıcı kaydedilemedi']);
        }

        if ($this->sendMail($user->email, $verificationToken)) {
            return response(['error' => 'E-posta gönderilemedi']);
        }

        return response(['message' => 'Onay e-postası tekrar gönderildi']);
    }

    public function verify(Request $request)
    {
        $token = $request->token;

        $user = Users::where('verificationToken', $token)->where('status', 1)->first();

        if($user->is_verified == "Yes")
        {
            return response(['error' => 'bu e-posta zaten onaylı']);
        }

        if (!$user) {
            return response(['error' => 'Invalid token or user not found']);
        }

        $expirationDate = $user->verification_token_expires_at;

        $currentDateTime = Carbon::now();

        if ($expirationDate > $currentDateTime) {
            return response(['error' => 'Token has expired']);
        }

        $user->is_verified = "Yes";
        $user->email_verified_at = $currentDateTime;

        if ($user->save()) {
            return response(['message' => 'Emailiniz başarıyla onaylandı']);
        } else {
            return response(['error' => 'Kullanıcı kaydedilemedi']);
        }
    }

    public function loadUser(Request $request)
    {
        $token = $request->bearerToken();

        if (empty($token)) {
            return response(['error' => 'Token not provided'], 401);
        }

        $user = Users::where('remember_token', $token)
            ->where('remember_me', 'Yes')
            ->where('status', 1)
            ->first();

        if (!$user) {
            return response(['error' => 'Session not found'], 401);
        }

        return response(['user' => $user, 'message' => 'Başarıyla oturuma giriş yapıldı', 'token' => $token]);
    }

    public function login(Request $request)
    {
        if (!empty($request->email) && !empty($request->password))
        {
            $email = $request->email;
            $user = Users::where(['status' => 1, 'email' => $email])->first();

            if ($user && Hash::check($request->password, $user->password))
            {
                if ($request->remember == 'Yes')
                {
                    $rememberToken = Str::random(60);

                    $user->remember_token = $rememberToken;
                    $user->remember_me = $request->remember;

                    if ($user->save())
                    {
                        return response(['message' => 'Hoş geldiniz oturum ' . $user->name, 'user' => $user, 'token' => $rememberToken]);
                    }
                    else
                    {
                        return response(['error' => 'Bir Hata Oluştu']);
                    }
                }
                else
                {

                    $user->remember_me = $request->remember;
                    $user->remember_token = "";

                    if ($user->save())
                    {
                        return response(['message' => 'Hoş geldin ' . $user->name, 'user' => $user]);
                    }
                    else
                    {
                        return response(['error' => 'bir hata oluştu']);
                    }

                }
            }
            else
            {
                return response(['error' => 'kullanıcı bulunamadı']);
            }

        }
        else
        {
            return response(['error' => 'boş alan bırakma']);
        }
    }




}
