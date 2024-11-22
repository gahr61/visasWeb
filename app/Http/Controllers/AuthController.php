<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

use App\Models\User;
use App\Models\VerificationToken;
use App\Mail\VerificationEmail;

class AuthController extends Controller
{

    /**
     * User login
     * @param \Illuminate\Http\Request $request [email, password]
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $credentials = request(['email', 'password']);

        /** Validate if email and password are valid */
        if(!\Auth::attempt($credentials)){
            return response()->json(['success'=>false, 'message' => 'Correo electrónico y/o contraseña incorrectos'], 401);
        }

        $user = $request->user();
        
        if(!$user->active){
            return response()->json(['success'=>false, 'message'=>'La cuenta esta inactiva. Contacte al administrador']);
        }

        $token = $user->createToken($user->id.$user->role);
        $token->expires_at = Carbon::now()->addWeek(1);

        $user_name = $user->names.
                        (!is_null($user->lastname1) ? ' '.$user->lastname1 : '').
                        (!is_null($user->lastname2) ? ' '.$user->lastname2 : '');

        return response()->json([
            'success' => true,
            'message' => 'Se inicio sesión corerctamente',
            'data' => [
                'access_token' => $token->plainTextToken,
                'user' => [
                    'id' => $user->id,
                    'name' => $user_name,
                    'change_password_required' => $user->change_password_required
                ],
                'expires_at' => Carbon::parse($token->expires_at)->toDateString()
            ]            
        ]);
    }

    /**
     * User logout
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(){
        try{
            \DB::beginTransaction();

            $user = auth()->user();
            $user->tokens()->delete(); //delete all user sessions

            \DB::commit();

            return response()->json([
                'message' => 'Session ended'
            ]);
        }catch(\Exception $e){
            \DB::rollback();

            return response()->json([
                'error' => 'Error: ( code: '.$e->getCode().', message: '.$e->getMessage().', line: '.$e->getLine().' in file AuthController)' 
            ]);
        }
    }

    /**
    * Register user
    * Generate email verifitacion log
    * Send email to user for email confirmation
    * @param \Illuminate\Http\Request [names, lastname1, lastname2, email, password] 
    * @return \Illuminate\Http\JsonResponse
    */
    public function register(Request $request){
        try{
            \DB::beginTransaction();

            $users = new User();
            $users->fill($request->all());
            $users->password = bcrypt($request->password);
            $users->role = 'client';
            $users->active = false; //is false because user must verify email
            $users->save();

            $token = bin2hex(random_bytes(18));
            VerificationToken::create([
                'users_id' => $users->id,
                'token' => hash('sha256', $token)
            ]);

            Mail::to($users->email)->send(new VerificationEmail($users, $token));

            \DB::commit();

            return response()->json([
                'status'=>200,
                'message'=>'El registro se realizo correctamente'
            ]);

        }catch(\Exception $ex){
            \DB::rollback();
            return response()->json(['error'=>'El usuario ya se encuentra registrado']);
        }
    }

    /**
     * Verify email address
     * Mark user as active
     * Remove verification token
     * @param \Illuminate\Http\Request [token] 
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyEmail(Request $request){
        try{
            \DB::beginTransaction();

            $token = hash('sha256', $request->token);

            $verificationToken = VerificationToken::where('token', $token)->first();

            if(!$verificationToken){
                return response()->json([
                    'message' => 'Token invalido o expirado'
                ], 400);
            }

            $timestamp = Carbon::now()->timestamp;

            $user = User::find($verificationToken->users_id);
            $user->email_verified_at = $timestamp;
            $user->active = true;
            $user->change_password_required = false;
            $user->save();

            $verificationToken->delete();

            \DB::commit();

            return response()->json([
                'message'=>'Su correo electrónico fue verificado. Por favor inicie sesión'
            ], 200);
        }catch(\Exception $ex){
            \DB::rollback();
            return response()->json($ex);
        }
    }
}
