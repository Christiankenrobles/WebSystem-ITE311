<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('index'); // Homepage
    }

    public function about(): string
    {
        return view('about'); // About Page
    }

    public function contact(): string
    {
        return view('contact'); // Contact Page
    }
}
