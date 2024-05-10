<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestEmail;

class TestController extends Controller
{
    public function sendTestEmail()
    {
        Mail::to('azharazeez49@gmail.com')->send(new TestEmail());
        return 'Test email sent!';
    }
}
