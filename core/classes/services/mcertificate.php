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
 * Classe de serviços com certificados.
 *
 * @category    Maestro
 * @package     Core
 * @subpackage  Services
 * @version     1.0
 * @since       1.0
 */
class MCertificate
{
    /**
     * Faz o parse de um certificado X509 e retorna um array com os dados
     * @param string $cert
     * @return array
     */
    public static function parseCertificateX509($cert)
    {
        $result = openssl_x509_parse($cert);
        return $result ?: openssl_x509_parse(self::derToPem($cert));
    }

    /**
     * Verifica se o certificado ainda já expirou.
     *
     * @param string $cert
     * @return boolean
     */
    public static function isExpired($cert)
    {
        $certInfo = self::parseCertificateX509($cert);
        return ($certInfo['validFrom_time_t'] > time() || $certInfo['validTo_time_t'] < time());
    }

    public static function isBinary($cert)
    {
        return !(openssl_x509_parse($cert));
    }

    /**
     * Converte um certificado do formato der para o pem.
     *
     * @param string $derData
     * @return string
     */
    public static function derToPem($derData)
    {
        $pem = chunk_split(base64_encode($derData), 64, "\n");
        $pem = "-----BEGIN CERTIFICATE-----\n" . $pem . "-----END CERTIFICATE-----\n";
        return $pem;
    }

    /**
     * converte um certificado do formato pem para der.
     *
     * @param string $pemData
     * @param bool $headers Se true, as duas primeiras linhas serão consideradas como cabeçalho e serão ignoradas.
     * @return string
     */
    public static function pemToDer($pemData, $headers = false)
    {
        $lines = explode("\n", trim($pemData));

        unset($lines[count($lines) - 1]);
        unset($lines[0]);

        if ($headers) {
            unset($lines[1]);
            unset($lines[2]);
        }

        $result = implode('', $lines);
        $result = base64_decode($result);

        return $result;
    }

    /**
     * Verifica a assinatura de uma mensagem.
     *
     * @param string $message
     * @param string $signature
     * @param string $certificate
     * @return boolean
     */
    public static function verifySignature($message, $signature, $certificate)
    {
        $answer = null;
        $result = 1;
        $prefix = self::createFilename();

        $messageFilePath = $prefix . '.txt';
        file_put_contents($messageFilePath, $message);
        $signatureFilePath = $prefix . '.sig';
        file_put_contents($signatureFilePath, $signature);
        $certificateFilePath = $prefix . '.pkcs7';
        file_put_contents($certificateFilePath, $certificate);

        exec("openssl smime -verify -inform PEM -in $signatureFilePath  -nointern -certfile $certificateFilePath -noverify  -content $messageFilePath", $answer, $result);

        unlink($messageFilePath);
        unlink($signatureFilePath);
        unlink($certificateFilePath);

        return $result == 0;
    }

    public static function verifyCertificate($certificate, $CAChain)
    {
        $answer = null;
        $result = 1;

        $certFilePath = self::createFilename() . '.crt';
        $caFilePath = self::createFilename() . '.ca.crt';

        file_put_contents($certFilePath, $certificate);
        file_put_contents($caFilePath, $CAChain);

        exec("openssl verify -CAfile $caFilePath $certFilePath", $answer, $result);

        unlink($certFilePath);
        unlink($caFilePath);

        return $result == 0;
    }

    /**
     * Extrai um certificado em formato PEM a partir de uma assinatura.
     *
     * @return mixed
     */
    public static function extractCertificateFromSignature($signature)
    {
        $answer = null;
        $result = 1;

        $filePrefix = self::createFilename();
        $certFilePath = $filePrefix . '.pkcs7';
        $signatureFilePath = $filePrefix . '.sig';

        file_put_contents($signatureFilePath, $signature);

        exec("openssl pkcs7 -in $signatureFilePath -inform PEM -print_certs -out $certFilePath", $answer, $result);

        if ($result == 0) {
            $fileContent = file_get_contents($certFilePath);
            unlink($certFilePath);
            $certificate = self::extractTopCertificate($fileContent);
        } else {
            mdump("Erro: não foi possivel extrair o certificado à partir da assinatura.");
            $certificate = null;
        }

        unlink($signatureFilePath);

        return $certificate;
    }

    /**
     * Função para extração de CPF conforme layout definido pelo ICP em:
     * http://www.receita.fazenda.gov.br/acsrf/LeiautedeCertificadosdaSRF.pdf
     *
     * Baseado na solução apresentada em:
     * http://grokbase.com/t/php/php-general/11br0qyh4t/retrieve-subjectaltname-from-client-certificate
     *
     * @param string $certDer Certificado em formato binário
     * @return string CPF
     */
    public static function extractCPF($certDer)
    {
        $pos = strpos($certDer, pack("H*", "4C010301"));
        return $pos > 0 ? substr($certDer, $pos + 16, 11) : null;
    }

    /**
     * Extrai o primeiro certificado de um arquivo texto contendo a cadeia de certificação.
     * @param string $certChain
     * @return string
     */
    private static function extractTopCertificate($certChain)
    {
        $strEndCertificate = "-----END CERTIFICATE-----";
        $endPos = strpos($certChain, $strEndCertificate) + strlen($strEndCertificate) + 1;

        return substr($certChain, 0, $endPos);
    }

    /**
     * Gera um prefixo para servir como nome de arquivo
     * @return string
     */
    private static function createFilename()
    {
        $fileDir = \Manager::getFilesPath();
        $filename = uniqid(rand(), true);

        return "$fileDir/$filename";
    }

}
