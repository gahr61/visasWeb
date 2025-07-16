<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WebContact;

class WebController extends Controller
{
    public function contact_form(Request $request){        
        $contacto = new WebContact();
        $contacto->fill($request->all());
        $contacto->save();

        $data = [
            'cuerpo'	=> [
                'name'	=> $request->name,
                'email'	=> $request->email,
                'phone'	=> $request->phone,
                'message'	=> $request->message
            ],
            'remitente'=>'postmaster@visas-premier.com',
            'asunto'=>'Contacto desde pagina web',
            'destinatario'=>'sistemas.ti.slp@gmail.com'
        ];

        $mail['data'] = $data;
        $route = 'contact.mail';

        \Mail::send($route, $mail, function($m) use($data){
            $m->from($data['remitente'], 'Contacto web');

            $m->to($data['destinatario'], $data['cuerpo']['name'])->subject($data['asunto']);
        });

        return 'OK';
        
    }
}
