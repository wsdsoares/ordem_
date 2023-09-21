<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\Usuario;

class Usuarios extends BaseController
{
    private $usuarioModel;
    private $grupoUsuarioModel;
    private $grupoModel;

    public function __construct()
    {
        $this->usuarioModel = new \App\Models\UsuarioModel();
        $this->grupoUsuarioModel = new \App\Models\GrupoUsuarioModel();
        $this->grupoModel = new \App\Models\GrupoModel();
    }

    /*======================================================================= */
    public function index()
    {

        $data = [
            'titulo' => 'Listando os usuários do sistema'
        ];

        return view('Usuarios/index', $data);
    }

    /*======================================================================= */
    public function recuperaUsuarios()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $atributos = ['id', 'nome', 'email', 'ativo', 'imagem', 'deletado_em'];

        $usuarios =
            $this->usuarioModel->select($atributos)
            ->withDeleted(true)
            ->orderBy('id', 'DESC')
            ->findAll();

        //receberá o array de objetos dos usuários
        $data = [];

        foreach ($usuarios as $usuario) {
            if ($usuario->imagem != null) {
                $imagem = [
                    'src'   => site_url("usuarios/imagem/$usuario->imagem"),
                    'class' => 'rounded-circle img-fluid',
                    'alt'   => esc($usuario->nome),
                    'width' => '50'
                ];
            } else {
                $imagem = [
                    'src'   => site_url("recursos/img/usuario_sem_imagem.png"),
                    'class' => 'rounded-circle img-fluid',
                    'alt'   => 'Usuário sem imagem',
                    'width' => '50'
                ];
            }
            $data[] = [
                // 'imagem' => ($usuario->imagem != null ? $usuario->imagem : '<span class="text-warning">Sem imagem</span>'),
                'imagem' => $usuario->imagem = img($imagem),
                'nome' => anchor("usuarios/exibir/$usuario->id", esc($usuario->nome), 'title="Exibir usuário ' . esc($usuario->nome) . '"'),
                'email' => esc($usuario->email),
                // 'ativo' => ($usuario->ativo == true ? '<i class="fa fa-unlock text-success"></i>&nbsp;Ativo' : '<i class="fa fa-lock text-warning"></i>&nbsp;Inativo'),
                'ativo' => $usuario->exibeSituacao(),
            ];
        }

        $retorno = [
            'data' => $data,
        ];

        return $this->response->setJSON($retorno);
    }

    /*======================================================================= */
    public function criar(int $id = NULL)
    {
        $usuario = new Usuario();
        // dd($usuario);
        $data = [
            'titulo' => "Criando novo usuário ",
            'usuario' => $usuario
        ];

        return view('Usuarios/criar', $data);
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

        //crio novo objeto da entidade usuários
        $usuario = new Usuario($post);

        if ($this->usuarioModel->protect(false)->save($usuario)) {
            $btnCriar = anchor("usuarios/criar", "Cadastrar novo usuário", ['class' => 'btn btn-danger mt-2']);
            session()->setFlashdata('sucesso', "Dados salvos com sucesso! <br>  $btnCriar");

            //retornamos o ultimo ID inserido na tabela de usuários
            $retorno['id'] = $this->usuarioModel->getInsertID();
            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor, verifique os erros abaixo e tente novamente!';
        $retorno['erros_model'] = $this->usuarioModel->errors();

        //Retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    /*======================================================================= */
    public function exibir(int $id = NULL)
    {
        $usuario = $this->buscaUsuarioOu404($id);
        $data = [
            'titulo' => "Detalhando o usuário " . esc($usuario->nome),
            'usuario' => $usuario
        ];

        return view('Usuarios/exibir', $data);
    }
    /*======================================================================= */
    public function editar(int $id = NULL)
    {
        $usuario = $this->buscaUsuarioOu404($id);
        $data = [
            'titulo' => "Editando o usuário " . esc($usuario->nome),
            'usuario' => $usuario
        ];

        return view('Usuarios/editar', $data);
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
        $usuario = $this->buscaUsuarioOu404($post['id']);

        //se não foi informado a senha removemos o $POST
        //se não fizermos dessa forma, o hashPassord fará o hash de uma string vazia - Before callbacks model
        if (empty($post['password'])) {
            unset($post['password']);
            unset($post['password_confirmation']);
        }

        //preenchemos os atributos do usuários com os valores do post
        $usuario->fill($post);

        if ($usuario->hasChanged() === false) {
            $retorno['info'] = 'Não há dados para serem atualizados';
            return $this->response->setJSON($retorno);
        }

        if ($this->usuarioModel->protect(false)->save($usuario)) {
            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor, verifique os erros abaixo e tente novamente!';
        $retorno['erros_model'] = $this->usuarioModel->errors();

        //Retorno para o ajax request
        return $this->response->setJSON($retorno);
    }
    /*======================================================================= */
    public function editarImagem(int $id = NULL)
    {
        $usuario = $this->buscaUsuarioOu404($id);
        $data = [
            'titulo' => "Alterando a imagem do usuário " . esc($usuario->nome),
            'usuario' => $usuario
        ];

        return view('Usuarios/editar_imagem', $data);
    }
    /*======================================================================= */
    public function upload()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        //envido o hash do token do form
        $retorno['token'] = csrf_hash();

        //regras de validação para arquivos
        $regras = [
            'imagem' => 'uploaded[imagem]|max_size[imagem,1024]|ext_in[imagem,png,jpg,jpeg,webp]',
        ];
        $mensagens = [   // Errors
            'imagem' => [
                'uploaded' => 'Por favor, escolha uma imagem',
                'max_size' => 'Por favor, escolha uma imagem de no máximo 1024 KB',
                'ext_in' => 'Por favor, escolha uma imagem (.png, .jpg, .jpeg, .webp)',
            ],
        ];
        $validacao = service('validation');

        $validacao->setRules($regras, $mensagens);

        if ($validacao->withRequest($this->request)->run() == false) {

            $retorno['erro'] = 'Por favor, verifique os erros abaixo e tente novamente!';
            $retorno['erros_model'] = $validacao->getErrors();

            //Retorno para o ajax request
            return $this->response->setJSON($retorno);
        }

        //recuperar o post da requisição
        $post = $this->request->getPost();

        //validando a existência do usuário
        $usuario = $this->buscaUsuarioOu404($post['id']);

        //recuperação da imagem que veio no POST
        $imagem = $this->request->getFile('imagem');

        list($largura, $altura) = getimagesize($imagem->getPathName());
        if ($largura < "300" || $altura < "300") {
            $retorno['erro'] = 'Por favor, verifique os erros abaixo e tente novamente!';
            $retorno['erros_model'] = ['dimensao' => 'A imagem não pode ser menor do que 300 x 300 pixels'];

            //Retorno para o ajax request
            return $this->response->setJSON($retorno);
        }

        $caminhoImagem = $imagem->store('usuarios');
        $caminhoImagem = WRITEPATH . "uploads/$caminhoImagem";

        //Manipular a imagem que está salva no diretório
        //Redimensionamento da imagem de 300 x 300 centralizada
        $this->manipulaImagem($caminhoImagem, $usuario->id);

        //a partir daqui será atualizado o BD

        $imagemAntiga = $usuario->imagem;

        $usuario->imagem = $imagem->getName();
        $this->usuarioModel->save($usuario);

        if ($imagemAntiga != null) {
            $this->removeImagemDoFileSystem($imagemAntiga);
        }

        session()->setFlashdata('sucesso', 'Imagem atualizada com sucesso!');

        //Retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    /*======================================================================= */
    public function imagem(string $imagem = null)
    {
        if ($imagem != null) {
            $this->exibeArquivo('usuarios', $imagem);
        }
    }

    /*======================================================================= */
    public function excluir(int $id = NULL)
    {
        $usuario = $this->buscaUsuarioOu404($id);

        if ($usuario->deletado_em != null) {
            return redirect()->back()->with('info', "Esse usuário já encontra-se Excluído!");
        }

        if ($this->request->getMethod() === 'post') {
            $this->usuarioModel->delete($usuario->id);
            if ($usuario->imagem != null) {
                $this->removeImagemDoFileSystem($usuario->imagem);
            }

            $usuario->imagem = null;
            $usuario->ativo = false;

            $this->usuarioModel->protect(false)->save($usuario);

            return redirect()->to(site_url('usuarios'))->with('sucesso', "Usuário $usuario->nome excluído com sucesso!");
        }

        $data = [
            'titulo' => "Excluindo o usuário " . esc($usuario->nome),
            'usuario' => $usuario
        ];

        return view('Usuarios/excluir', $data);
    }

    /*======================================================================= */
    public function desfazerexclusao(int $id = NULL)
    {
        $usuario = $this->buscaUsuarioOu404($id);

        if ($usuario->deletado_em == null) {
            return redirect()->back()->with('info', "Apenas usuários excluídos podem ser recuperados!");
        }

        $usuario->deletado_em = null;

        $this->usuarioModel->protect(false)->save($usuario);
        return redirect()->back()->with('sucesso', "Usuário $usuario->nome recuperado com sucesso!");
    }

    /*======================================================================= */
    public function grupos(int $id = NULL)
    {
        $usuario = $this->buscaUsuarioOu404($id);

        $usuario->grupos = $this->grupoUsuarioModel->recuperaGruposDoUsuario($usuario->id, 5);
        $usuario->pager = $this->grupoUsuarioModel->pager;

        $data = [
            'titulo' => "Gerenciando os grupos de acesso do usuário " . esc($usuario->nome),
            'usuario' => $usuario
        ];


        //Quando o usuário for um cliente, podemos retornar a view de exibição do usuário, 
        //informando que ele é um cliente e não é possível adicioná-los aos outros grupos ou remover de um grupo existe.
        $grupoCliente = 2;
        if (in_array($grupoCliente, array_column($usuario->grupos, 'grupo_id'))) {
            return redirect()->to(site_url("usuarios/exibir/$usuario->id"))
                ->with('info', "Esse usuário é um cliente, portanto, não é necessário atribuí-lo ou removê-lo de outros grupos de acesso");
        }
        $grupoAdmin = 1;
        if (in_array($grupoAdmin, array_column($usuario->grupos, 'grupo_id'))) {
            $usuario->full_control = true; //está no grupo de admin, então podemos retornar a view
            return view('Usuarios/grupos', $data);
        }
        $usuario->full_control = false; // não está no grupo admin, podemos seguir com o processamento

        if (!empty($usuario->grupos)) {
            //recuperamos os grupos que o usuário ainda não faz parte
            $gruposExistentes = array_column($usuario->grupos, 'grupo_id');


            $data['gruposDisponiveis'] = $this->grupoModel
                ->where('id !=', 2)   //Não recuperamos o gurpo de clientesdd
                ->whereNotIn('id', $gruposExistentes)
                ->findAll();
        } else {
            // recuperamos todos os grupos, cin exceção do grupo ID 2, do usuário
            $data['gruposDisponiveis'] = $this->grupoModel
                ->where('id !=', 2)
                ->findAll();
        }

        return view('Usuarios/grupos', $data);
    }
    /*======================================================================= */
    public function salvarGrupos()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        //envido o hash do token do form
        $retorno['token'] = csrf_hash();

        //recuperar o post da requisição
        $post = $this->request->getPost();

        //validando a existência do usuário
        $usuario = $this->buscaUsuarioOu404($post['id']);

        if (empty($post['grupo_id'])) {
            $retorno['erro'] = 'Por favor, verifique os erros abaixo e tente novamente!';
            $retorno['erros_model'] = ['grupo_id' => 'Escolha um ou mais grupos para salvar!'];

            //Retorno para o ajax request
            return $this->response->setJSON($retorno);
        }

        if (in_array(2, $post['grupo_id'])) {
            $retorno['erro'] = 'Por favor, verifique os erros abaixo e tente novamente!';
            $retorno['erros_model'] = ['grupo_id' => 'O grupo de clientes não pode ser atribuido de forma manual'];

            //Retorno para o ajax request
            return $this->response->setJSON($retorno);
        }

        // se vier o grupo adm, os demais são irrelevantes, pelo fato do adm ter todas as permissões
        if (in_array(1, $post['grupo_id'])) {
            $grupoAdmin = [
                'grupo_id' => 1,
                'usuario_id' => $usuario->id
            ];

            //associamos o usuário ao grupo vindo da view
            $this->grupoUsuarioModel->insert($grupoAdmin);
            //remove todos os demais grupos que estão associados ao usuário em questão
            $this->grupoUsuarioModel->where('grupo_id !=', 1)
                ->where('usuario_id =', $usuario->id)
                ->delete();

            session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
            session()->setFlashdata('info', 'Notamos que o grupo ADMINISTRADOR foi informado, portanto não há a necessidade de informar outros grupos, pois apenas o Grupo ADMINISTRADOR será associado ao usuário!');
            return $this->response->setJSON($retorno);
        }

        //Receberá as permissões do POST
        $grupoPush = [];

        foreach ($post['grupo_id'] as $grupo) {
            array_push($grupoPush, [
                'grupo_id' => $grupo,
                'usuario_id' => $usuario->id
            ]);
        }

        $this->grupoUsuarioModel->insertBatch($grupoPush);

        session()->setFlashdata('sucesso', 'Dados salvos com sucesso!');
        return $this->response->setJSON($retorno);
    }
    /*======================================================================= */
    public function removeGrupo(int $principal_id = null)
    {
        if ($this->request->getMethod() === 'post') {
            $grupoUsuario = $this->buscaGrupoUsuarioOu404($principal_id);
            if ($grupoUsuario->grupo_id == 2) {
                return redirect()->to(site_url("usuarios/exibir/$grupoUsuario->usuario_id"))->with("info", "Não é permitida a exclusão do usuário Grupo CLIENTES");
            }

            $this->grupoUsuarioModel->delete($principal_id);
            return redirect()->back()->with("sucesso", "Usuário removido do grupo de acesso com sucesso!");
        }

        return redirect()->back();
    }

    /*======================================================================= */
    public function editarSenha()
    {
        $data = [
            'titulo' => 'Edite sua senha de acesso'
        ];
        return view('Usuarios/editar_senha', $data);
    }

    /*======================================================================= */
    public function atualizarsenha()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        //envido o hash do token do form
        $retorno['token'] = csrf_hash();

        $current_password = $this->request->getPost('current_password');

        $usuario =  usuario_logado();

        if ($usuario->verificaPassword($current_password) === false) {
            $retorno['erro'] = 'Por favor, verifique os erros abaixo e tente novamente';
            $retorno['erros_model'] = ['current_password' => 'Senha atual inválida'];
            return $this->response->setJSON($retorno);
        }

        $usuario->fill($this->request->getPost());

        if ($usuario->hasChanged() === false) {
            $retorno['info'] = 'Não há dados para serem atualizados';
            return $this->response->setJSON($retorno);
        }

        if ($this->usuarioModel->save($usuario)) {
            $retorno['sucesso'] = 'Senha atualizada com sucesso!';
            return $this->response->setJSON($retorno);
        }

        $retorno['erro'] = 'Por favor, verifique os erros abaixo e tente novamente!';
        $retorno['erros_model'] = $this->usuarioModel->errors();

        //Retorno para o ajax request
        return $this->response->setJSON($retorno);
    }

    /*======================================================================= */
    /**
     * Método que recupera o usuário
     * @param integer id
     * @return Exceptions | Object
     */
    private function buscaUsuarioOu404(int $id = null)
    {
        if (!$id || !$usuario = $this->usuarioModel->withDeleted(true)->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o usuário $id");
        }
        return $usuario;
    }

    /*======================================================================= */
    /**
     * Método que recupera o registro do grupo associado ao usuário
     * @param integer $principal)id
     * @return Exception|Object
     */

    private function buscaGrupoUsuarioOu404(int $principal_id = null)
    {
        if (!$principal_id || !$grupoUsuario = $this->grupoUsuarioModel->find($principal_id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o registro de associação ao grupo de acesso $principal_id");
        }
        return $grupoUsuario;
    }



    /*======================================================================= */
    private function removeImagemDoFileSystem(string $imagem)
    {
        $caminhoImagem = WRITEPATH . "uploads/usuarios/$imagem";
        if (is_file($caminhoImagem)) {
            unlink($caminhoImagem);
        }
    }

    /*======================================================================= */
    private function manipulaImagem(string $caminhoImagem, int $usuario_id)
    {
        service('image')
            ->withFile($caminhoImagem)
            ->fit(300, 300, 'center')
            ->save($caminhoImagem);

        $anoAtual = date('Y');
        // Adicionar uma marca d'agua de texto
        \Config\Services::image('imagick')
            ->withFile($caminhoImagem)
            ->text("Ordem $anoAtual - User-ID $usuario_id", [
                'color'      => '#fff',
                'opacity'    => 0.5,
                'withShadow' => false,
                'hAlign'     => 'center',
                'vAlign'     => 'bottom',
                'fontSize'   => 10,
            ])
            ->save($caminhoImagem);
    }
}
