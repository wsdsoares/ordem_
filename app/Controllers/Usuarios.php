<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\Usuario;

class Usuarios extends BaseController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new \App\Models\UsuarioModel();
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

        $atributos = ['id', 'nome', 'email', 'ativo', 'imagem'];

        $usuarios =
            $this->usuarioModel->select($atributos)
            ->orderBy('id', 'DESC')
            ->findAll();

        //receberá o array de objetos dos usuários
        $data = [];

        foreach ($usuarios as $usuario) {

            $data[] = [
                // 'imagem' => ($usuario->imagem != null ? $usuario->imagem : '<span class="text-warning">Sem imagem</span>'),
                'imagem' => $usuario->imagem,
                'nome' => anchor("usuarios/exibir/$usuario->id", esc($usuario->nome), 'title="Exibir usuário ' . esc($usuario->nome) . '"'),
                'email' => esc($usuario->email),
                'ativo' => ($usuario->ativo == true ? '<i class="fa fa-unlock text-success"></i>&nbsp;Ativo' : '<i class="fa fa-lock text-warning"></i>&nbsp;Inativo'),
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

        if ($usuario->hasChanged() == false) {
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
        service('image')
            ->withFile($caminhoImagem)
            ->fit(300, 300, 'center')
            ->save($caminhoImagem);


        //se não foi informado a senha removemos o $POST
        //se não fizermos dessa forma, o hashPassord fará o hash de uma string vazia - Before callbacks model
        if (empty($post['password'])) {
            unset($post['password']);
            unset($post['password_confirmation']);
        }

        //preenchemos os atributos do usuários com os valores do post
        $usuario->fill($post);

        if ($usuario->hasChanged() == false) {
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
}
