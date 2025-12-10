<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    //
    public function index(): View
    {
        $users = DB::select("SELECT * FROM users");

        return view('welcome', ['users' => $users]);
    }
}
