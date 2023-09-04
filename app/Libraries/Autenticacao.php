<?php

namespace App\Libraries;

class Autenticacao
{
  private $usuario;
  private $usuarioModel;

  public function __construct()
  {
    $this->usuarioModel = new \App\Models\UsuarioModel();
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
    $session->regenerate();

    //setamos na sessão o id do usário
    $session->set('usuario_id', $usuario->id);
  }
}
