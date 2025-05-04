<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//import untuk logout
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exception\JWTException;
use Tymon\JWTAuth\Exception\TokenExpiredException;
use Tymon\JWTAuth\Exception\TokenInvalidException;

class LogoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        //mnghapus token
        $removeToken = JWTAuth::invalidate(JWTAuth::getToken());

        //kalau token berhasil di hapus
        if($removeToken){
            //memberikan respon json
            return response()->json([
                'success'   => true,
                'message'   => 'Berhasil Log Out'
            ]);
            
            
        }
    }
}
