<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Usuario extends Entity
{
    protected $dates   = ['criado_em', 'atualizado_em', 'deletado_em'];

    public function exibeSituacao()
    {
        if ($this->deletado_em != null) {
            $icone = '<span class="text-white">Excluído</span>&nbsp<i class="fa fa-undo"></i>&nbsp Desfazer';
            $situacao = anchor("usuarios/desfazerexclusao/$this->id", $icone, ['class' => 'btn btn-outline-success btn-sm']);

            return $situacao;
        }
        if ($this->ativo == true) {
            return '<i class="fa fa-unlock text-success"></i>&nbsp;Ativo';
        }
        if ($this->ativo == false) {
            return '<i class="fa fa-lock text-warning"></i>&nbsp;Inativo';
        }
    }
    /**
     * Método que verifica se a senha é true ou false
     * @param string $password
     * @return boolean
     */

    public function verificaPassword(string $password): bool
    {
        return password_verify($password, $this->password_hash);
    }

    /*======================================================================= */
    /**
     * Método que valida se o usuário logado possui a permissão para visualizar/ acessar determianda roda
     * @param string $permissao
     * @return boolean
     */

    public function temPermissaoPara(string $permissao): bool
    {
        // se o usuário logado é ADMIN retorna-se true
        if ($this->is_admin == true) {
            return true;
        }
        //Se o usuário logado ($this) possui o atributo 'permissoes' vazio (empty), então retorna-se false, pois a $Permissao não estará no array $permissoes
        //Essa situação acontece quando o usuário logad ($this) faz parte de um grupo que não possui permissões ou não está em nenhum grupo de acesso
        //
        if (empty($this->permissoes)) {
            return false;
        }
        //nesse caso o usuário logado possui o array de permissões
        if (in_array($permissao, $this->permissoes) == false) {
            return false;
        }

        return true;
    }
}
