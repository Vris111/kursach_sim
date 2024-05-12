<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function user(Request $request)
    {
        $user = Auth::user();
        $response = [
            'name' => $user->name,
            'surname' => $user->surname,
            'patronymic' => $user->patronymic,
            'date_of_birth' => $user->date_of_birth,
            'country' => $user->country,
            'email' => $user->email,
        ];
        return response()->json($response);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'surname' => 'required|string',
            'patronymic' => 'required|string',
            'date_of_birth' => 'required|date',
            'country' => 'required|string',
            'email' => 'required|string|unique:users',
            'telephone_number' => 'required|numeric|min:11|unique:users',
            'password' => 'required|string|min:6',
        ],[
            'name.required' => 'Имя обязательно для заполнения',
            'surname.required' => 'Фамилия обязательна для заполнения',
            'patronymic.required' => 'Отчество обязательно для заполнения',
            'date_of_birth.required' => 'Дата рождения обязательна для заполнения',
            'country.required' => 'Страна обязательна для заполнения',
            'email.required' => 'Email обязателен для заполнения',
            'email.unique' => 'Email уже занят',
            'password.required' => 'Пароль обязателен для заполнения',
            'password.min' => 'Пароль должен быть не менее 6 символов',
            'telephone_number.required' => 'Номер телефона должен быть не менее 11 символов',
            'telephone_number.min' => 'Номер телефона обязателен для заполнения',
            'telephone_number.unique' => 'Номер телефона уже используется',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user = new User([
            'name' => $request->input('name'),
            'surname' => $request->input('surname'),
            'patronymic' => $request->input('patronymic'),
            'date_of_birth' => $request->input('date_of_birth'),
            'country' => $request->input('country'),
            'telephone_number' => $request->input('telephone_number'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password'))
        ]);
        $user->save();

        $token = $user->createToken('token')->plainTextToken;

        $cookie = cookie('jwt', $token, 60 * 24);

        return response([
            'message' => 'User has been registered',
            'token' => $token,
        ], 200)->withCookie($cookie);
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))){
            return response([
                'message' => 'Invalid Email or Password'
            ], Response::HTTP_UNAUTHORIZED);
        }
        $user = Auth::user();

        $token = $user->createToken('token')->plainTextToken;

        $cookie = cookie('jwt', $token, 60 * 24);

        return response([
            'message' => 'Login Successful',
            'token' => $token,
        ])->withCookie($cookie);

    }

    public function logout() {
        $cookie = Cookie::forget('jwt');
        return response([
            'message' => 'Logout Successful',
        ])->withCookie($cookie);
    }
}
