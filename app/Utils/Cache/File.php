<?php

namespace App\Utils\Cache;

class File
{
    /**
     * Retorna o caminho até o arquivo de cache
     * @param string $hash
     * @return string
     */
    private static function getFilePath($hash)
    {
        // Diretório de chache
        $dir = getenv('CACHE_DIR');

        // Verifica a existência do diretótio
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        // Retorna o caminho até o arquivo
        return $dir.'/'.$hash;
    }

    /**
     * Guarda as informações de retorno do Response no cache
     * @param string $hash
     * @param mixed $content
     * @return boolean
     */
    private static function storageCache($hash, $content)
    {
        // Serializa o retorno de Response para poder ser gravado em cache
        $serialize = serialize($content);

        // Obtém o caminho até o arquivo de cache
        $cacheFile = self::getFilePath($hash);

        // Grava as informações no arquivo
        return file_put_contents($cacheFile, $serialize);
    }

    /**
     * Retorna o conteúdo gravado no cache
     * @param string $hash
     * @param integer $expiration
     * @return mixed
     */
    private static function getContentCache($hash, $expiration)
    {
        // Obtém o caminho do arquivo
        $cacheFile = self::getFilePath($hash);

        // Verifica a existência do arquivo
        if (!file_exists($cacheFile)) {
            return false;
        }

        // Valida a expiração do cache
        $createTime = filectime($cacheFile);
        $diffTime = time() - $createTime;
        if ($diffTime > $expiration) {
            return false;
        }

        // Retorna o dado real
        $serialize = file_get_contents($cacheFile);
        return unserialize($serialize);
    }

    /**
     * Obtém informação do cache
     * @param string $hash
     * @param interger $expiration
     * @param Closure $function
     * @return mixed
     */
    public static function getCache($hash, $expiration, $function)
    {
        // Verifica o conteúdo gravado
        if ($content = self::getContentCache($hash, $expiration)) {
            return $content;
        }

        // Execução da função
        $content = $function();

        // Grava o retorno no cache
        self::storageCache($hash, $content);

        // Retorna o conteúdo
        return $content;
    }
}
