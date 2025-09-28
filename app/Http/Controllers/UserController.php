<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Auth\AuthManager;

class UserController extends Controller
{
    public function index(User $user, AuthManager $auth){
        $ifExist = $user->isAdmin();
        $ifLogged = $auth->check();
        
        return Inertia::render('profile/Index', [
            'isAdmin' => $ifExist,
            'isLogged' => $ifLogged
        ]);
    }

    public function test(){
        return Inertia::render('Test');
    }
}