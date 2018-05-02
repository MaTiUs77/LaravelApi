<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiController extends Controller
{

    public function __construct()
    {
        $this->user = new User;
    }

    public function home()
    {
        $motor = 'laravel';
        $version = app()::VERSION;
        $github = env('GITHUB');
        $api_tag = env('API_TAG');
        $api_host_remoto = env('API_HOST_REMOTO');
        $server_time = Carbon::now();

        return compact('motor','version','github','api_tag','api_host_remoto','server_time');
    }

    public function login(Request $request) {
        $credentials = $request->only('username', 'password');
        $token = null;

        // Validar inputs
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        $validator = Validator::make($credentials, $rules);
        if($validator->fails()) {
            return response()->json([
                'response' => 'error',
                'message' => $validator->messages()
            ]);
        }

        // Iniciar login
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'response' => 'error',
                    'message' => 'invalid_username_or_password',
                ]);
            }
        } catch (JWTException $e) {
            return response()->json([
                'response' => 'error',
                'message' => 'failed_to_create_token',
            ]);
        }

        // Si todo salio bien, retorno el token
        return response()->json([
            'response' => 'success',
            'result' => [
                'token' => $token,
            ],
        ]);
    }

    public function logout(Request $request) {
        $this->validate($request, ['token' => 'required']);

        try {
            JWTAuth::invalidate($request->input('token'));
            return response()->json(['success' => true, 'message'=> "You have successfully logged out."]);
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['success' => false, 'error' => 'Failed to logout, please try again.'], 500);
        }
    }

    public function register(Request $request)
    {
        $credentials = $request->only('username', 'email', 'password');

        $rules = [
            'username' => 'required|unique:users',
            'password' => 'required'
        ];

        $validator = Validator::make($credentials, $rules);
        if($validator->fails()) {
            return response()->json(['success'=> false, 'error'=> $validator->messages()]);
        }

        $username = $request->username;
        $email = $request->email;
        $password = $request->password;

        $user = User::create([
            'username' => $username,
            'email' => $email,
            'password' => bcrypt($password)
        ]);

        return response()->json([
            'success'=> true,
            'message'=> 'Usuario registrado con exito.'
        ]);
    }

    public function getAuthUser(Request $request){
        $user = JWTAuth::toUser($request->token);
        return response()->json(['result' => $user]);
    }

}