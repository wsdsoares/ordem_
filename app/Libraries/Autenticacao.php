<?php

namespace App\Libraries;

class Autenticacao
{
  private $usuario;
  private $usuarioModel;
  private $grupoUsuarioModel;

  public function __construct()
  {
    $this->usuarioModel = new \App\Models\UsuarioModel();
    $this->grupoUsuarioModel = new \App\Models\GrupoUsuarioModel();
  }
  /**
   * Método que realzia o login na aplicação
   * @param string $email
   * @param string $password
   * @return boolean
   */

  public function login(string $email, string $password): bool
  {
    $usuario = $this->usuarioModel->buscaUsuarioPorEmail($email);

    if ($usuario === null) {
      return false;
    }

    //Verificamos se a senha é válida
    if ($usuario->verificaPassword($password) == false) {
      return false;
    }

    if ($usuario->ativo == false) {
      return false;
    }

    $this->logaUsuario($usuario);

    return true;
  }

  /*======================================================================= */
  /**
   * Método que realzia o encerramento da sessão
   * @return void
   */

  public function logout(): void
  {
    session()->destroy();
  }

  /*======================================================================= */
  public function pegaUsuarioLogado()
  {
    if ($this->usuario === null) {
      $this->usuario = $this->pegaUsarioDaSessao();
    }
    return $this->usuario;
  }
  /*======================================================================= */
  /**
   * Método que verifica se o usuário está logado
   * @return boolean
   */
  public function estaLogado(): bool
  {
    return $this->pegaUsuarioLogado() !== null;
  }

  /*======================================================================= */
  /**
   * Método que insere na seção o id do usuário
   * @param string $usuario
   * @return boolean
   */
  private function logaUsuario(object $usuario): void
  {
    $session = session();

    //gerando uma sessão nova para id - 
    //antes de inserir o id do usuário na sessão, gerar um novo ID da sessão
    // $session->regenerate();
    $_SESSION['__ci_last_regenerate'] = time();

    //setamos na sessão o id do usário
    $session->set('usuario_id', $usuario->id);
  }

  /*======================================================================= */
  /**
   * Método que recupera da sessão e valida usuário logado
   * @param object $usuario
   * @return null|object
   */
  private function pegaUsarioDaSessao()
  {
    if (session()->has('usuario_id') == false) {
      return null;
    }

    //Busca o usuário na base de dados
    $usuario = $this->usuarioModel->find(session()->get('usuario_id'));

    //validação se o usuário existe e se o mesmo tem a permissão de login na aplicação
    if ($usuario == null || $usuario->ativo == false) {
      return null;
    }

    //definimos as permissoes do usuário logado
    $usuario = $this->definiePermissoesDoUsuarioLogado($usuario);

    return $usuario;
  }



  /*======================================================================= */
  /**
   * Método que verifica se o usuário na sessão (session()->get('usuario_id')) logado está asssociado ao grupo de ADMIN
   * @return null|object
   */

  private function isAdmin(): bool
  {
    //Definimos o ID do grupo ADMIN
    //Esse ID não pode ser alterado
    $grupoAdmin = 1;

    //verifica se o usuário logado está no grupo administrador
    $administrador = $this->grupoUsuarioModel->usuarioEstaNoGrupo($grupoAdmin, session()->get('usuario_id'));

    //Verifica se foi encontrado algum registro
    if ($administrador == null) {
      return false;
    }

    return true;
  }



  /*======================================================================= */
  /**
   * Método que verifica se o usuário na sessão (session()->get('usuario_id')) logado está asssociado ao grupo de CLIENTE
   * @return null|object
   */

  private function isCliente(): bool
  {
    //Definimos o ID do grupo ADMIN
    //Esse ID não pode ser alterado
    $grupoCliente = 2;

    //verifica se o usuário logado está no grupo Cliente
    $cliente = $this->grupoUsuarioModel->usuarioEstaNoGrupo($grupoCliente, session()->get('usuario_id'));

    //Verifica se foi encontrado algum registro
    if ($cliente == null) {
      return false;
    }

    return true;
  }


  /*======================================================================= */
  /**
   * Método que define as permissoes que o usuário logado possui
   * Usuário exclusivamente no método pegaUsuarioda sessão
   * @param object $usuario
   * @return null|object
   */


  public function definiePermissoesDoUsuarioLogado(object $usuario): object
  {
    //definimos se o usuário logado é ADM
    //esse atributo será utilizado no metodo temPermissaoPara() na entity Usuario
    $usuario->is_admin = $this->isAdmin();

    //se for ADM então não é cliente
    if ($usuario->is_admin == true) {
      $usuario->is_cliente = false;
    } else {

      //nesse ponto verifica se o usuário é um cliente, visto que não é um ADM
      $usuario->is_cliente = $this->isCliente();
    }

    //Só recuperamos as permissoes de um usuário que não seja admin e não seja cliente
    //pois esses dois grupos não possuem permissoes
    //o atributo $usuario->permissoes será examinado na Entity Usuario para verificarmos se 
    //o mesmo pode ou não visualizar e acessar alguma rota
    // se o usuário logado possui o atributo $usuario->permissoes é porque não é ADMIN e não é CLIENTE
    if ($usuario->is_admin == false && $usuario->is_cliente == false) {
      $usuario->permissoes = $this->recuperaPermissoesDoUsuarioLogado();
    }

    // Nesse ponto já definimos se é admin ou se é cliente
    // caso não seja nem admin e nem cliente, entã o objetopossui o atributo permissões
    // que pode ou não estar vazio
    // Portanto, podemos retornar $usuario
    return $usuario;
  }




  /*======================================================================= */
  /**
   * Método que retorna as permissoes do usuário logado 
   * @return array
   */

  private function recuperaPermissoesDoUsuarioLogado(): array
  {
    $permissoesDoUsuario = $this->usuarioModel->recuperaPermissoesDoUsuarioLogado(session()->get('usuario_id'));

    return array_column($permissoesDoUsuario, 'permissao');
  }
}
