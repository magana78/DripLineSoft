<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HelloController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => 'Hello World from Mobile Controller 🚀'
        ]);
    }
}
