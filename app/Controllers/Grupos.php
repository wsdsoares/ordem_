<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\Grupo;

class Grupos extends BaseController
{
    private $grupoModel;

    public function __construct()
    {
        $this->grupoModel = new \App\Models\GrupoModel();
    }

    /*======================================================================= */
    public function index()
    {
        $data = [
            'titulo' => 'Listando os grupos de acesso ao sistema'
        ];

        return view('Grupos/index', $data);
    }

    /*======================================================================= */
    public function recuperaGrupos()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $atributos = ['id', 'nome', 'descricao', 'exibir', 'deletado_em'];

        $grupos =
            $this->grupoModel->select($atributos)
            ->withDeleted(true)
            ->orderBy('id', 'DESC')
            ->findAll();

        //receberá o array de objetos dos usuários
        $data = [];

        foreach ($grupos as $grupo) {

            $data[] = [
                'nome' => anchor("grupos/exibir/$grupo->id", esc($grupo->nome), 'title="Exibir grupo ' . esc($grupo->nome) . '"'),
                'descricao' => esc($grupo->descricao),
                'exibir' => $grupo->exibeSituacao(),
            ];
        }

        $retorno = [
            'data' => $data,
        ];

        return $this->response->setJSON($retorno);
    }

    /*======================================================================= */
    public function exibir(int $id = NULL)
    {
        $grupo = $this->buscaGrupoOu404($id);
        $data = [
            'titulo' => "Detalhando o usuário " . esc($grupo->nome),
            'grupo' => $grupo
        ];

        return view('Grupos/exibir', $data);
    }
    /*======================================================================= */
    /**
     * Método que recupera o grupo
     * @param integer id
     * @return Exceptions | Object
     */
    private function buscaGrupoOu404(int $id = null)
    {
        if (!$id || !$grupo = $this->grupoModel->withDeleted(true)->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o grupo $id");
        }
        return $grupo;
    }
}