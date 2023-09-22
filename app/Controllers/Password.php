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
        // $data = [
        //     'titulo' => ''
        // ];

        echo 'Willian';
        exit('test');

        // if (!$this->request->isAJAX()) {
        //     return redirect()->back();
        // }

        // //envido o hash do token do form
        // $retorno['token'] = csrf_hash();

        // //recuperar o post da requisição
        // $post = $this->request->getPost();

        // echo '<pre>';
        // print_r($post);
        // exit;
    }
}
