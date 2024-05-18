<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use DateTime;
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
            'passport_series' => $user->passport_series,
            'passport_number' => $user->passport_number,
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
            'passport_series' => 'required|min:4|max:4',
            'passport_number' => 'required|min:6|max:6',
            'password' => 'required|string|min:6',
        ],[
            'name.required' => 'The name is required',
            'surname.required' => 'Last name is required',
            'patronymic.required' => 'The patronymic is required to fill in',
            'date_of_birth.required' => 'Date of birth is required',
            'country.required' => 'The country is required to fill in',
            'email.required' => 'Email is required to fill in',
            'email.unique' => 'The email is already taken',
            'password.required' => 'The password is required',
            'password.min' => 'The password must be at least 6 characters long',
            'telephone_number.required' => 'The phone number must be at least 11 characters long',
            'telephone_number.min' => 'The phone number is required to fill in',
            'telephone_number.unique' => 'The phone number is already in use',
            'passport_series.required' => 'The passport series is required to be filled in',
            'passport_series.min' => 'The passport series must be at least 4 characters long',
            'passport_series.max' => 'The passport series must be no more than 4 characters long',
            'passport_number.required' => 'The passport number is required to be filled in',
            'passport_number.min' => 'The passport number must be at least 4 characters long',
            'passport_number.max' => 'The passport number must be no more than 4 characters',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $dateOfBirth = $request->input('date_of_birth');
        $now = new DateTime();
        $age = $now->diff(new DateTime($dateOfBirth))->y;
        if ($age < 18) {
            return response()->json(['error' => 'You must be at least 18 years old to register'], 422);
        }

        $passportSeries = $request->input('passport_series');
        $passportNumber = $request->input('passport_number');

        $existingUser = User::where('passport_series', $passportSeries)->where('passport_number', $passportNumber)
            ->first();

        if ($existingUser) {
            return response()->json(['error' => 'The passport series and number are already in use'], 422);
        }

        $user = new User([
            'name' => $request->input('name'),
            'surname' => $request->input('surname'),
            'patronymic' => $request->input('patronymic'),
            'date_of_birth' => $request->input('date_of_birth'),
            'country' => $request->input('country'),
            'telephone_number' => $request->input('telephone_number'),
            'passport_series' => $request->input('passport_series'),
            'passport_number' => $request->input('passport_number'),
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
