<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GrupoTempSeeder extends Seeder
{
    public function run()
    {
        $grupoModel = new \App\Models\GrupoModel();

        $grupos = [
            [
                'nome' => 'Administrador',
                'descricao' => 'Grupo com acesso total ao sistema',
                'exibir' => false
            ],
            [
                'nome' => 'Clientes',
                'descricao' => 'Esse grupo é destionado para atribuição de clientes, pois os mesmos poderão logar no sistema para acessar suas ordens de serviços',
                'exibir' => false
            ],
            [
                'nome' => 'Atendentes',
                'descricao' => 'Esse grupo acessa ao sistema para realziar o atendimento aos clientes',
                'exibir' => false
            ],

        ];

        foreach ($grupos as $grupo) {
            $grupoModel->skipValidation(true)->insert($grupo);
        }

        echo "Grupos criado com sucesso! <br/>";
    }
}
