<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\Token;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';

    protected $returnType       = 'App\Entities\Usuario';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'nome',
        'email',
        'password',
        'reset_hash',
        'reset_expira_em',
        'imagem',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';
    protected $deletedField  = 'deletado_em';

    // Validation
    protected $validationRules = [
        'id'                    => 'permit_empty|is_natural_no_zero', // <-- ESSA LINHA DEVE SER ADICIONADA
        'nome'                  => 'required|min_length[3]|max_length[125]',
        'email'                 => 'required|valid_email|max_length[230]|is_unique[usuarios.email,id,{id}]', // Não pode ter espaços
        'password'              => 'required|min_length[6]',
        'password_confirmation' => 'required_with[password]|matches[password]'
    ];
    protected $validationMessages = [
        'nome' => [
            'required' => 'O campo nome é obrigatório.',
            'min_length' => 'O campo nome precisa ter pelo menos 3 cacteres.',
            'max_length' => 'O campo nome não pode ter mais de 125 cacteres.',
        ],
        'email' => [
            'required' => 'O campo email é obrigatório.',
            'max_length' => 'O campo nome não pode ter mais de 230 cacteres.',
            'is_unique' => 'Esse email já está sendo utilizado. Por favor, informe outro.',
        ],
        'password' => [
            'required' => 'O campo senha é obrigatório.',
            'min_length' => 'O campo senha não pode ter menos de 6 cacteres.',
        ],
        'password_confirmation' => [
            'required_with' => 'Por favor, repita sua senha no campo "confirmação de senha".',
            'matches'       => 'As senhas precisam ser iguais.',
        ],
    ];

    // Callbacks
    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
            unset($data['data']['password']);
            unset($data['data']['password_confirmation']);
        }

        return $data;
    }

    /*======================================================================= */
    /**
     * Método que recupera o usuário para logar na aplicação
     * @param string $email
     * @return null|object
     */

    public function buscaUsuarioPorEmail(string $email)
    {
        return $this->where('email', $email)->where('deletado_em', null)->first();
    }

    /*======================================================================= */
    /**
     * Método que recupera as permissoes do usuário para logado na aplicação
     * @param int $usuario_id
     * @return null|array
     */

    public function recuperaPermissoesDoUsuarioLogado(int $usuario_id)
    {
        $atributos = [
            // 'usuarios.id',
            // 'usuarios.nome AS usuario',
            // 'grupos_usuarios.*',
            'permissoes.nome AS permissao',
        ];

        return $this->select($atributos)
            ->asArray() // recuperamos no formato array
            ->join('grupos_usuarios', 'grupos_usuarios.usuario_id = usuarios.id')
            ->join('grupos_permissoes', 'grupos_permissoes.grupo_id = grupos_usuarios.grupo_id')
            ->join('permissoes', 'permissoes.id = grupos_permissoes.permissao_id')
            ->where('usuarios.id', $usuario_id)
            ->groupBy('permissoes.nome')
            ->findAll();
    }
    /*======================================================================= */
    /**
     * Método que recupera o usuário de acordo com o hash do token
     * @param int $token
     * @return null|object
     */
    public function buscaUsuarioParaRedefinirSenha(string $token)
    {
        //instanciando o objeto da classe, passando como parâmetro o token
        $token = new Token($token);
        $tokenHash = $token->getHash();

        $usuario = $this->where('reset_hash', $tokenHash)
            ->where('deletado_em', null)
            ->first();

        if ($usuario === null) {
            return null;
        }

        if ($usuario->reset_expira_em < date('Y-m-d H:i:s')) {
            return null;
        }

        return $usuario;
    }
}
