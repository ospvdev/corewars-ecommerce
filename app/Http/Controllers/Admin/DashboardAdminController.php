<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardAdminController extends Controller{

    public function index(User $user){
        $ifExist = $user->isAdmin();

        if ($ifExist) {
            $arrayInfo = [
                "isAdmin" => $ifExist,
                "user" => [
                    "name" => 'Dionni',
                    "role" => 'admin',
                ]
            ];
        } else {
            $arrayInfo = [
                "isAdmin" => $ifExist,
                "message" => "No tienes permisos para ver esta página.",
            ];
        }

        dd($arrayInfo);
        die();

        // return Inertia::render('admin/Index', [
        //     'data' => $arrayInfo,
        // ]);
    }
}