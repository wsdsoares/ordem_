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
        $autenticacao = service('autenticacao');
        // $autenticacao->login('asd@email.com', '123456');
        $autenticacao->login('email@hotmail.com', '123456');
        $usuario  = $autenticacao->pegaUsuarioLogado();

        dd($usuario);

        // $autenticacao->logout();
        // return redirect()->to(site_url('/'));
    }
}
