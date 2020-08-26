<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Admin;

class AdminController extends Controller
{
    public function __construct(Admin $admin) {
        $this->admin = $admin;
    }

    public function get() {
        $data = json_decode($this->admin->getData());

        dd($data);
    }
}
