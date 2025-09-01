<?php

namespace App\Http\Controllers;

use App\Mail\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;


class EmailController extends Controller
{
    public function sendEmail() {
        $recipient = 'pablo.lorenzo@farmagram.com.ar'; // Change to the recipient's email address
        $data = [
            'name' => 'Pablo Lorenzo',
            'username' => 'Plorenzo',
            'welcomeMessage' => 'Prueba de correo electronico.',
            'startLink' => 'https://example.com/get-started'
        ];
        $sql_gp='select * from pop00101;'; 
        $gptest = collect(DB::connection('gp')->select($sql_gp));
        Mail::to($recipient)->send(new Notificacion($data, $gptest));

        return response()->json(['message' => 'Email sent successfully to ' . $recipient]);
    }
}
