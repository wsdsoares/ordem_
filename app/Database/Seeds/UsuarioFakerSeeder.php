<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsuarioFakerSeeder extends Seeder
{
    public function run()
    {
        $usuarioModel = new \App\Models\UsuarioModel();

        $faker = \Faker\Factory::create();

        $criarQuantosUsuarios = 5000;
        $usuriosPush = [];

        for ($i = 0; $i < $criarQuantosUsuarios; $i++) {
            array_push($usuriosPush, [
                'nome' => $faker->unique()->name,
                'email' => $faker->unique()->email,
                'password_hash' => '123456',
                'ativo' => $faker->numberBetween(0, 1),
            ]);
        }
        // echo '<pre>';
        // print_r($usuriosPush);
        // echo '</pre>';
        // exit;

        $usuarioModel->skipValidation(true)
            ->protect(false)
            ->insertBatch($usuriosPush);

        echo "$criarQuantosUsuarios usuarios criados com sucesso";
    }
}
