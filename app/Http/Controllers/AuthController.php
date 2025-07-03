<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Http\Resources\AuthResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['register', 'login']);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50',
            'username' => 'required|string|max:20|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'phone_number' => 'nullable|string|max:15'
        ]);

        // Password
        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        Auth::login($user);

        return response()->json(new AuthResource($user));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        // Create Token
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::where('email', $request->email)->first();
            $success['token'] =  $user->createToken('onlineshop', ['*'], now()->addWeek())->plainTextToken;
            $success['user'] =  new AuthResource($user);

            return response()->json($success);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Email or Password is still incorrect'
            ], 403);
        }
    }


    public function logout(Request $request)
    {
        $token = $request->user()?->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function profile()
    {
        $user = Auth::user();

        return response()->json([
            'data' => new UserResource($user)
        ]);
    }

    public function editprofile(Request $request)
    {
        $user = User::find(Auth::id());

        $data = $request->validate([
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:3072',
            'name' => 'required|string|max:50',
            'username' => 'required|string|max:20|unique:users,username,' .  $user->id,
            'email' => 'required|email|unique:users,email,' .  $user->id,
            'phone_number' => 'nullable|string|max:15'
        ]);

        // Avatar
        if ($request->hasFile('avatar') && $request->file('avatar') instanceof UploadedFile) {
            $filename = $user->updateAvatar($request->file('avatar'), $data['name']);
            $data['avatar'] = $filename;
        }

        $user->update($data);

        return response()->json(new UserResource($user));
    }

    public function changepassword(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $request->validate([
            'current_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|same:new_password'
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'The current password is incorrect'
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(new UserResource($user));
    }
}
