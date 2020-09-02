<?php

namespace App\Http\Controllers;

use App\Admin;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function __construct(Admin $admin) {
        $this->admin = $admin;
    }

    public function getData(Request $request) {
        $response = json_decode($this->admin->getData($request));
        return response()->json($response);
    }
}
