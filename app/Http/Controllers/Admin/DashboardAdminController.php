<?php

namespace App\Http\Controllers\Admin;

class DashboardAdminController extends AdminController
{
    public function index(){
        return $this->renderAdmin('admin/dashboard/Ecommerce', 'eCommerce');
    }

    public function products(){
        return $this->renderAdmin('admin/products/Products', 'Products');
    }
}