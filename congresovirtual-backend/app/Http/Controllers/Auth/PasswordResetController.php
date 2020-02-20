<?php

namespace App\Http\Controllers\Auth;

use App\PasswordReset;
use App\User;
use App\Http\Controllers\Controller;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Webpatser\Uuid\Uuid;

class PasswordResetController extends Controller
{
    /**
     * Create token password reset
     *
     * @param  [string] email
     * @return [string] message
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email'
        ]);
        if($validator->fails()) {
            return response()->json($validator->errors(), 412);
        }

        $user = User::where('email', $request->email)->first();
        if(!$user) {
            return response()->json([
                'message' => 'We can\'t find a user with that e-mail address.'], 404);
        }

        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => Uuid::generate()->string
            ]
        );

        if($user && $passwordReset) {
            $user->notify(new PasswordResetRequest($passwordReset->token));
        }
        return response()->json([
            'message' => 'We have e-mailed your password reset link!'], 201);
    }

    /**
     * Find token password reset
     *
     * @param  [string] $token
     * @return [string] message
     * @return [json] passwordReset object
     */
    public function find($token)
    {
        $passwordReset = PasswordReset::where('token', $token)->first();
        if(!$passwordReset) {
            return response()->json([
                'message' => 'This password reset token is invalid.'], 404);
        }
        if(Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return response()->json([
                'message' => 'This password reset token is invalid.'], 404);
        }
        return response()->json($passwordReset);
    }

    /**
     * Reset password
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @param  [string] token
     * @return [string] message
     * @return [json] user object
     */
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:8|max:20|confirmed',
            'token' => 'required|string'
        ]);
        if($validator->fails()) {
            return response()->json($validator->errors(), 412);
        }

        $passwordReset = PasswordReset::where('token', $request->token)->first();
        if(!$passwordReset) {
            return response()->json([
                'message' => 'This password reset token is invalid.'], 404);
        }

        $user = User::where('email', $request->email)->first();
        if(!$user) {
            return response()->json([
                'message' => 'We can\'t find a user with that e-mail address.'], 404);
        }

        $user->password = bcrypt($request->password);
        $user->save();
        $passwordReset->delete();
        $user->notify(new PasswordResetSuccess($passwordReset));
        return response()->json($user);
    }
}
