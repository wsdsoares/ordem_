<?php

namespace App\Models;

use CodeIgniter\Model;

class GrupoModel extends Model
{
    protected $table            = 'grupos';

    protected $returnType       = 'App\Entities\Grupo';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['nome', 'descricao', 'exibir'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Validation
    protected $validationRules = [
        'id'                    => 'permit_empty|is_natural_no_zero', // <-- ESSA LINHA DEVE SER ADICIONADA
        'nome'                  => 'required|max_length[128]|is_unique[grupos.nome,id,{id}]',
        'descricao'             => 'required|min_length[240]',
    ];
    protected $validationMessages = [
        'nome' => [
            'required'      => 'O campo nome é obrigatório.',
            'max_length'    => 'O campo nome não pode ter mais de 128 cacteres.',
            'is_unique'     => 'Esse grupo já está sendo utilizado. Por favor, informe outro nome!',
        ],
        'descricao' => [
            'required'       => 'O campo descrição é obrigatório.',
            'max_length'     => 'O campo descrição não pode ter mais de 240 cacteres.',

        ]
    ];
}
