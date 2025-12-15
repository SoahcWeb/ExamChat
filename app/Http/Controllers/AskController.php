<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class AskController extends Controller
{
    public function index()
    {
        return Inertia::render('Ask/Index');
    }
}
