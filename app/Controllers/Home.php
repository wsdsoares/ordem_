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

    public function email()
    {
        $email = service('email');

        $email->setFrom('no-replay@ordem.com', 'Ordem de Servico INC');
        $email->setTo('wilhaod@diginey.com');
        // $email->setCC('another@another-example.com');
        // $email->setBCC('them@their-example.com');

        $email->setSubject('Recuperação de senha');
        $email->setMessage('Iniciando a recuperação de senha - Atualizado');


        if ($email->send()) {
            echo 'Email enviado';
        } else {
            $email->printDebugger();
            $email->send();
        }
    }
}
