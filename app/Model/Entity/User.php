<?php

namespace App\Model\Entity;

use WilliamCosta\DatabaseManager\Database;

class User
{
    /**
     * ID do usuário
     * @var integer
     */
    public $id;

    /**
     * Nome do usuário
     * @var string
     */
    public $nome;

    /**
     * E-mail do usuário
     * @var string
     */
    public $email;

    /**
     * Senha do usuário
     * @var string
     */
    public $senha;

    public function cadastrar()
    {
        // Insere no banco de dados
        $this->id = (new Database('usuarios'))->insert([
            'nome' => $this->nome,
            'email' => $this->email,
            'senha' => $this->senha
        ]);

        // Sucesso
        return true;
    }

    /**
     * Atualiza usuário no banco de dados
     */
    public function atualizar()
    {
        return (new Database('usuarios'))->update('id = '.$this->id, [
            'nome' => $this->nome,
            'email' => $this->email,
            'senha' => $this->senha
        ]);
    }

    /**
     * Exclui um usuário no banco de dados
     */
    public function excluir() {
        return (new Database('usuarios'))->delete('id = '.$this->id);
    }

    /**
     * Retorna uma instância com base no id
     * @param integer
     * @return User
     */
    public static function getUserById($id) {
        return self::getUsers('id = '.$id)->fetchObject(self::class);
    }

    /**
     * Retorna um usuário com base em e-mail
     * @param string
     * @return User
     */
    public static function getUserByEmail($email) {
        return self::getUsers('email = "'.$email.'"')->fetchObject(self::class);
    }
        /**
     * Retorna os depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getUsers($where = null, $order = null, $limit = null, $fields = '*') {
        return (new Database('usuarios'))->select($where, $order, $limit, $fields);
    }
}
