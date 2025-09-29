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

        $permissions = $this->user->roles->flatMap(fn($role) => $role->permissions->pluck('slug'))->unique()->values()->toArray();
        $baseProps = [
            'btnActive' => $btnActive,
            'user' => [
                'haveAccess' => $canAccess,
                'name' => $this->user->name,
                'role' => optional($this->user->roles->pluck('name'))->toArray()[0] ?? null,
                'permissions' => [
                    'products' => [
                        'canView' => in_array('view-products', $permissions),
                        'canCreate' => in_array('create-products', $permissions),
                        'canEdit' => in_array('update-products', $permissions),
                        'canDelete' => in_array('delete-products', $permissions),
                    ],
                    'orders' => [
                        'canView' => in_array('view-orders', $permissions),
                        'canCreate' => in_array('create-orders', $permissions),
                        'canEdit' => in_array('update-orders', $permissions),
                        'canDelete' => in_array('delete-orders', $permissions),
                    ],
                    'users' => [
                        'canView' => in_array('view-users', $permissions),
                        'canCreate' => in_array('create-users', $permissions),
                        'canEdit' => in_array('update-users', $permissions),
                        'canDelete' => in_array('delete-users', $permissions),
                    ]
                ],
            ],
        ];

        return Inertia::render($component, array_merge($baseProps, $extraProps));
    }
}