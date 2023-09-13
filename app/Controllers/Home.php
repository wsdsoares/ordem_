<?php

namespace App\Controllers;

use App\Libraries\Autenticacao;

class Home extends BaseController
{
    public function index(): string
    {
        $data = [
            'titulo' => 'Home'
        ];
        return view('Home/index', $data);
    }

    public function login()
    {
        $autenticacao = new Autenticacao();
        // $autenticacao->login('asd@email.com', '123456');
        $autenticacao->login('asd@email.com', '123456');
        // dd($autenticacao->pegaUsuarioLogado());


        // $autenticacao->logout();
        // return redirect()->to(site_url('/'));
    }
}
