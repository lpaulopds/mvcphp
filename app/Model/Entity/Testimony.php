<?php

namespace App\Model\Entity;

use WilliamCosta\DatabaseManager\Database;

class Testimony
{
    /**
     * ID do depoimento
     * @var integer
     */
    public $id;

    /**
     * Nome do usuário que fez o depoimento
     * @var string
     */
    public $nome;

    /**
     * Mensagem depoimento
     * @var string
     */
    public $mensagem;

    /**
     * Data da publicação do depoimento
     * @var string
     */
    public $data;

    /**
     * Cadastra a instância atual no banco de dados
     * @return boolean
     */
    public function cadastrar()
    {
        // Define a data
        $this->data = date('Y-m-d H:i:s');

        // Insere o depoimento no banco de dados
        $this->id = (new Database('depoimentos'))->insert([
            'nome' => $this->nome,
            'mensagem' => $this->mensagem,
            'data' => $this->data
        ]);

        // Sucesso
        return true;
    }

    /**
     * Atualiza os dados do banco de dados com a instância atual
     * @return boolean
     */
    public function atualizar()
    {
        // Atualiza o depoimento no banco de dados
        return (new Database('depoimentos'))->update('id = '.$this->id, [
            'nome' => $this->nome,
            'mensagem' => $this->mensagem
        ]);
    }

    /**
     * Exclui depoimento do banco de dados
     * @return boolean
     */
    public function excluir() {
        // Exclui o depoimento do banco de dados
        return (new Database('depoimentos'))->delete('id = '.$this->id);
    }

    /**
     * Retorna depoimento pelo id
     * @param integer
     * @return Testimony
     */
    public static function getTestimonyById($id) {
        return self::getTestimonies('id = '.$id)->fetchObject(self::class);
    }

    /**
     * Retorna os depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $fields
     * @return PDOStatement
     */
    public static function getTestimonies($where = null, $order = null, $limit = null, $fields = '*') {
        return (new Database('depoimentos'))->select($where, $order, $limit, $fields);
    }
}
