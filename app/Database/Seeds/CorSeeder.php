<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CorSeeder extends Seeder
{
    public function run()
    {
        //
        $corModel = new \App\Models\CorModel();

        $cores = [
            [
                'nome' => 'Amarela',
                'descricao' => 'Descrição Amarela',
            ],
            [
                'nome' => 'Azul',
                'descricao' => 'Descrição Azul',
            ],
            [
                'nome' => 'Vermelha',
                'descricao' => 'Descrição Vermelha',
            ],
            [
                'nome' => 'Verde',
                'descricao' => 'Descrição Verde',
            ],
            [
                'nome' => 'Branca',
                'descricao' => 'Descrição Branca',
            ],
            [
                'nome' => 'Preta',
                'descricao' => 'Descrição Preta',
            ],
        ];

        // dd($cores);

        foreach ($cores as $cor) {
            $corModel->insert($cor);
        }

        echo 'Cores inseridas com successo';
    }
}
