<?php

namespace App\Http\Controllers\Admin;

class DashboardAdminController extends AdminController
{
    public function index(){
        return $this->renderAdmin('admin/dashboard/Ecommerce', 'eCommerce');
    }
}