<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//import validator
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        //mengatur validator
        $validator = Validator::make($request->all(),[
            'email'     =>'required',
            'password'  =>'required'
        ]);

        //verifikasi kalau gagal validasi
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }

        //mengambil kredensial dari request
        $credentials = $request->only('email','password');

        //kalau autentikasi gagal
        if(!$token = auth()->guard('api')->attempt($credentials)){
            return response()->json([
                'success' =>false,
                'message' =>'Email atau Password salah',
            ],401);
        }

        //kalau autentikasi berhasil
        return response()->json([
            'success'   => true,
            'user'      => auth()->guard('api')->user(),
            'token'     => $token
        ],200);
    }
}
