<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getAllUser()
    {
        $users = User::all();

        if($users){
            return ResponseFormatter::success( $users, 'Data User Berhasil Diambil');
        } else {
            return ResponseFormatter::error( null, 'Data User Tidak Ada', 404);
        }
    }

    public function getUserById($id)
    {
        $users = User::find($id);

        if($users){
            return ResponseFormatter::success( $users, 'Data User Berhasil Diambil');
        } else {
            return ResponseFormatter::error( null, 'Data User Tidak Ada', 404);
        }
    }
}
