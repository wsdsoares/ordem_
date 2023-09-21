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
}
