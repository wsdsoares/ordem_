<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Password extends BaseController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new \App\Models\UsuarioModel();
    }
    public function esqueci()
    {
        $data = [
            'titulo' => 'Esqueci minha senha'
        ];

        return view('Password/esqueci', $data);
    }

    public function processaEsqueci()
    {
        $data = [
            'titulo' => ''
        ];

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        //envido o hash do token do form
        $retorno['token'] = csrf_hash();

        //recuperar o email da requisição
        $email = (string) $this->request->getPost('email');
        $usuario = $this->usuarioModel->buscaUsuarioPorEmail($email);

        if ($usuario === null || $usuario->ativo === false) {
            $retorno['erro'] = 'Não encontramos uma conta válida com esse e-mail.';
            return $this->response->setJSON($retorno);
        }

        $usuario->iniciaPasswordReset();

        $this->usuarioModel->save($usuario);

        /**
         * @todo enviar e-mail de recuperação
         */

        return $this->response->setJSON([]);
    }

    public function resetenviado()
    {

        $data = [
            'titulo' => 'E-mail de recuperação enviado para a sua caixa de entrada.'
        ];

        return view('Password/reset_enviado', $data);
    }
}
