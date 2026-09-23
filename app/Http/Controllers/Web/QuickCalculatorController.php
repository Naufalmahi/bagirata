<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QuickCalculatorController extends Controller
{
    public function index()
    {
        return view('calculator.index');
    }
}
