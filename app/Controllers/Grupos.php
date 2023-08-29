<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\Grupo;

class Grupos extends BaseController
{
    private $grupoModel;
    private $grupoPermissaoModel;

    public function __construct()
    {
        $this->grupoModel = new \App\Models\GrupoModel();
        $this->grupoPermissaoModel = new \App\Models\GrupoPermissaoModel();
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
    public function criar()
    {
        $grupo = new Grupo();

        $data = [
            'titulo' => "Criando um nov grupo de acesso",
            'grupo' => $grupo
        ];

        return view('Grupos/criar', $data);
    }
    /*======================================================================= */
    public function cadastrar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        //envido o hash do token do form
        $retorno['token'] = csrf_hash();

        //recuperar o post da requisição
        $post = $this->request->getPost();

        //crio novo objeto da entidade grupo
        $grupo = new Grupo($post);

        if ($this->grupoModel->save($grupo)) {
            $btnCriar = anchor("grupos/criar", "Cadastrar novo grupo", ['class' => 'btn btn-danger mt-2']);
            session()->setFlashdata('sucesso', "Dados salvos com sucesso! <br>  $btnCriar");

            //retornamos o ultimo ID inserido na tabela de usuários
            $retorno['id'] = $this->grupoModel->getInsertID();
            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor, verifique os erros abaixo e tente novamente!';
        $retorno['erros_model'] = $this->grupoModel->errors();

        //Retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    /*======================================================================= */
    public function exibir(int $id = NULL)
    {
        $grupo = $this->buscaGrupoOu404($id);
        $data = [
            'titulo' => "Detalhando o grupo de acesso " . esc($grupo->nome),
            'grupo' => $grupo
        ];

        return view('Grupos/exibir', $data);
    }

    /*======================================================================= */
    public function editar(int $id = NULL)
    {
        $grupo = $this->buscaGrupoOu404($id);

        if ($grupo->id < 3) {
            return redirect()->back()->with('atencao', 'O grupo <b>' . esc($grupo->nome) . '</b> não pode ser editado ou excluído, conforme detalhado na exibição do mesmo');
        }
        $data = [
            'titulo' => "Editando o grupo de acesso " . esc($grupo->nome),
            'grupo' => $grupo
        ];

        return view('Grupos/editar', $data);
    }

    /*======================================================================= */
    public function atualizar()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        //envido o hash do token do form
        $retorno['token'] = csrf_hash();

        //recuperar o post da requisição
        $post = $this->request->getPost();

        //validando a existência do usuário
        $grupo = $this->buscaGrupoOu404($post['id']);


        //garantia de que os grupos ADMI E CLIENTES Não possam ser editados
        if ($grupo->id < 3) {

            $retorno['erro'] = 'Por favor, verifique os erros abaixo e tente novamente!';
            $retorno['erros_model'] = ['grupo' => 'O grupo <b class="text-white">' . esc($grupo->nome) . '</b> não pode ser editado ou excluído, conforme detalhado na exibição do mesmo'];
            return $this->response->setJSON($retorno);
        }

        //preenchemos os atributos do usuários com os valores do post
        $grupo->fill($post);

        if ($grupo->hasChanged() == false) {
            $retorno['info'] = 'Não há dados para serem atualizados';
            return $this->response->setJSON($retorno);
        }

        if ($this->grupoModel->protect(false)->save($grupo)) {
            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor, verifique os erros abaixo e tente novamente!';
        $retorno['erros_model'] = $this->grupoModel->errors();

        //Retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    /*======================================================================= */
    public function excluir(int $id = NULL)
    {
        $grupo = $this->buscaGrupoOu404($id);

        if ($grupo->id < 3) {
            return redirect()->back()->with('atencao', 'O grupo <b>' . esc($grupo->nome) . '</b> não pode ser editado ou excluído, conforme detalhado na exibição do mesmo');
        }

        if ($grupo->deletado_em != null) {
            return redirect()->back()->with('info', "Esse grupo de acesso já encontra-se Excluído!");
        }

        if ($this->request->getMethod() === 'post') {
            $this->grupoModel->delete($grupo->id);

            return redirect()->to(site_url('grupos'))->with('sucesso', 'Grupo de acesso' . esc($grupo->nome) . 'excluído com sucesso!');
        }

        $data = [
            'titulo' => "Excluindo o grupo de acesso " . esc($grupo->nome),
            'grupo' => $grupo
        ];

        return view('Grupos/excluir', $data);
    }

    /*======================================================================= */
    public function desfazerexclusao(int $id = NULL)
    {
        $grupo = $this->buscaGrupoOu404($id);

        if ($grupo->deletado_em == null) {
            return redirect()->back()->with('info', "Apenas grupos excluídos podem ser recuperados!");
        }

        $grupo->deletado_em = null;

        $this->grupoModel->protect(false)->save($grupo);
        return redirect()->back()->with('sucesso', 'Grupo ' . esc($grupo->nome) . 'recuperado com sucesso!');
    }

    /*======================================================================= */
    public function permissoes(int $id = NULL)
    {
        $grupo = $this->buscaGrupoOu404($id);

        if ($grupo->id == 1) {
            return redirect()->back()->with('atencao', 'Não é nessário atribuir ou remover permissões de acesso para o grupo <b>' . esc($grupo->nome) . '</b>, pois esse grupo é Administrador!');
        }

        if ($grupo->id == 2) {
            return redirect()->back()->with('atencao', 'Não é nessário atribuir ou remover permissões de acesso para o grupo <b>' . esc($grupo->nome) . '</b>!');
        }

        //recuperação das permissoes
        if ($grupo->id == 2) {
            $grupo->permissoes = $this->grupoPermissaoModel->recuperaPermissoesDoGrupo($grupo->id, 5);
            $grupo->pager = $this->grupoPermissaoModel->pager;
        }

        dd($grupo);

        $data = [
            'titulo' => "Gerenciando as permissões do grupo de acesso " . esc($grupo->nome),
            'grupo' => $grupo
        ];

        return view('Grupos/permissoes', $data);
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
