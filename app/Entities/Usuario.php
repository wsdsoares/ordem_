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
}
