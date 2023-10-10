<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Login extends BaseController
{
    public function novo()
    {
        $data = [
            'titulo' => 'Realize o login'
        ];
        return view("Login/novo", $data);
    }

    public function criar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        //envido o hash do token do form
        $retorno['token'] = csrf_hash();

        $email = $this->request->getPost("email");
        $password = $this->request->getPost("password");

        //capturando a instância do serviço autenticação
        $autenticacao = service('autenticacao');

        if ($autenticacao->login($email, $password) === false) {
            //Credenciais inválidas
            $retorno['erro'] = 'Por favor, verifique os erros abaixo e tente novamente!';
            $retorno['erros_model'] = ['credenciais' => 'Não encontramos suas credenciais de acesso'];
            return $this->response->setJSON($retorno);
        }

        //Credenciais validadas

        //Recuperando o usuário logado
        $usuarioLogado = $autenticacao->pegaUsuarioLogado();
        session()->setFlashdata('sucesso', "Olá $usuarioLogado->nome, que bom que está de volta!");

        if ($usuarioLogado->is_cliente) {
            $retorno['redirect'] = 'ordens/minhas';
            return $this->response->setJSON($retorno);
        }
        //Usuário normal, exceto cliente
        $retorno['redirect'] = 'home';
        return $this->response->setJSON($retorno);
    }

    public function logout()
    {
        //capturando a instância do serviço autenticação
        $autenticacao = service('autenticacao');
        $usuarioLogado = $autenticacao->pegaUsuarioLogado();
        $autenticacao->logout();

        return redirect()->to(site_url("login/mostramensagemlogout/$usuarioLogado->nome"));
    }

    public function mostraMensagemlogout($nome = null)
    {
        return redirect()->to(site_url("login"))->with("sucesso", "$nome, esperamos ver você novamente!");
    }
}
