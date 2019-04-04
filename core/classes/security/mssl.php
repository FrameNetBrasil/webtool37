<?php
/* Copyright [2011, 2013, 2017] da Universidade Federal de Juiz de Fora
 * Este arquivo é parte do programa Framework Maestro.
 * O Framework Maestro é um software livre; você pode redistribuí-lo e/ou
 * modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada
 * pela Fundação do Software Livre (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil,
 * mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer
 * MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL
 * em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título
 * "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software
 * Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a
 * Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

/**
 * Classe para agrupar serviços de encriptação e decriptação utilizando SSL.
 *
 * @author Marcello
 */
class MSSL
{

    /**
     * Gera um par de chaves Publica/Privada.
     *
     * @param int $size Tamanho em bits da chave
     * @return array Chaves pública e privada
     */
    public static function generateKeyPair($size = 4096)
    {
        $config = array(
            "digest_alg" => "sha512",
            "private_key_bits" => $size,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );

        $privKey = null;
        $res = openssl_pkey_new($config);
        openssl_pkey_export($res, $privKey);
        $pubKey = openssl_pkey_get_details($res);
        $pubKey = $pubKey["key"];

        return array('public' => $pubKey, 'private' => $privKey);
    }

    /**
     * Criptografia assimétrica: usa uma chave privada para descriptografar
     * o conteúdo criptografado com uma chave pública.
     *
     * @param string $data Conteúdo criptografado
     * @param string $privKey Chave privada
     * @param bool $base64Decode Informa de $data deve ser convertido de base64
     * @return type Valor decriptografado ou null em caso de erro.
     */
    public static function decryptPrivate($data, $privKey, $base64Decode = true)
    {
        $decoded = $base64Decode ? base64_decode($data) : $data;
        $decrypted = null;
        openssl_private_decrypt($decoded, $decrypted, $privKey);

        return $decrypted;
    }

    /**
     * Criptografia simétrica.
     *
     * @param string $data Conteúdo em texto puro a ser criptografado.
     * @param string $key Chave criptográfica.
     * @param string $method Método utilizado para criptografia. Padrão AES256.
     * @param type $iv Vetor de inicialização.
     * @return binary Dados criptografados
     */
    public static function simmetricEncrypt($data, $key, $method = 'aes256', $iv = '0000000000000000')
    {
        return base64_encode(openssl_encrypt($data, $method, $key, 0, $iv));
    }

    /**
     * Decriptografia assimétrica.
     *
     * @param binary $encrypted Conteúdo criptografado.
     * @param type $key Chave criptográfica.
     * @param type $method Método utilizado na criptografia.
     * @param type $iv Vetor de inicialização.
     * @return string Dados decriptografados.
     */
    public static function simmetricDecrypt($encrypted, $key, $method = 'aes256', $iv = '0000000000000000')
    {
        return openssl_decrypt(base64_decode($encrypted), $method, $key, 0, $iv);
    }

    /**
     * Gera uma string aleatória. Útil para senhas ou chaves temporárias para criptografia simétrica.
     * @param int $size Tamanho da string
     * @param string $alphabet Caracteres que serão utilizados para a geração da string
     * @return string
     */
    public static function randomString($size,
                                        $alphabet = 'abcdefghijklmopqrstuvxzABCDEFGHIJKLMNOPQRSTUVXZ0123456789_-+=@#!$()')
    {

        $string = '';
        for ($i = 0; $i < $size; $i++) {
            $string .= $alphabet[mt_rand(0, strlen($alphabet) - 1)];
        }

        return $string;
    }
}
