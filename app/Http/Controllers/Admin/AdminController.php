<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function (Request $request, $next) {
            $this->user = $request->user();

            if ($this->user) {
                $this->user->loadMissing('roles');
            }

            return $next($request);
        });
    }

    protected function renderAdmin(string $component, string $btnActive, array $extraProps = []){
        $canAccess = method_exists($this->user, 'isAdmin') ? $this->user->isAdmin() : false;
        if (!$canAccess) {
            return redirect()->route('home');
        }

        $baseProps = [
            'btnActive' => $btnActive,
            'user' => [
                'haveAccess' => $canAccess,
                'name' => $this->user->name,
                'role' => optional($this->user->roles->pluck('name'))->toArray()[0] ?? null,
            ],
        ];

        return Inertia::render($component, array_merge($baseProps, $extraProps));
    }
}