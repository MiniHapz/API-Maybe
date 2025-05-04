<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//import dulu yang dibutuhkan
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function __invoke (Request $request)
    {
        //atur validasi
        $validator = Validator::make($request->all(),[
            'name'      =>'required',
            'email'     =>'required|email|unique:users',
            'password'  =>'required|min:8|confirmed',
        ]);

        //info kalau validasi gagal
        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }

        //membuat users
        $user=User::create([
            'name'      =>$request->name,
            'email'     =>$request->email,
            'password'  =>bcrypt($request->password),
        ]);

        //memberikan respon json (berhasil)
        if($user){
            return response()->json([
                'success'   =>true,
                'user'      =>$user,
            ],201);
        }

        //respon kalau gagal
    }
    
}
