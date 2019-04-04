<?php
/* Copyright [2011, 2012, 2013] da Universidade Federal de Juiz de Fora
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
 * Brief Class Description.
 * Complete Class Description.
 */
class MUtil
{

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $value1 (tipo) desc
     * @param $value2 (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function NVL($value1, $value2)
    {
        return ($value1 != null) ? $value1 : $value2;
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $value1 (tipo) desc
     * @param $value2 (tipo) desc
     * @param $value3 (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function ifNull($value1, $value2, $value3)
    {
        return ($value1 == null) ? $value2 : $value3;
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param &$value1 (tipo) desc
     * @param $value2 (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function setIfNull(&$value1, $value2)
    {
        if ($value1 == null) {
            $value1 = $value2;
        }
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param &$value1 (tipo) desc
     * @param $value2 (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public function setIfNotNull(&$value1, $value2)
    {
        if ($value2 != null) {
            $value1 = $value2;
        }
    }

    /**
     * @todo TRANSLATION
     * Retorna o valor booleano da variÃ¡vel
     * FunÃ§Ã£o utilizada para testar se uma variÃ¡vel tem um valor booleano, conforme definiÃ§Ã£o: serÃ¡ verdadeiro de
     *      for 1, t ou true... caso contrÃ¡rio serÃ¡ falso.
     *
     * @param $value (misc) valor a ser testado
     *
     * @returns (bool) value
     *
     */
    public static function getBooleanValue($value)
    {
        $trues = array('t', '1', 'true', 'True');

        if (is_bool($value)) {
            return $value;
        }

        return in_array($value, $trues);
    }

    /**
     * Retorna o valor float da variável, com base no locale atual (definido via setlocale)
     *
     * @param $value (string) valor a ser convertido
     * @returns (float) value
     *
     */
    public static function getFloatValue($value)
    {
        $l = localeConv();
        $sign = (strpos($value, $l['negative_sign'] ?: '-') !== false) ? -1 : 1;
        $value = strtr($value, $l['positive_sign'] . $l['negative_sign'] . '()',
            '    ');
        $value = str_replace(' ', '', $value);
        $value = str_replace($l['currency_symbol'] ?: '$', '', $value);
        $value = str_replace($l['mon_thousands_sep'] ?: ',', '', $value);
        $value = str_replace($l['mon_decimal_point'] ?: '.', '.', $value);
        return (float)($value * $sign);
    }

    /**
     * @todo TRANSLATION
     * Retorna o valor da variÃ¡vel sem os caracteres considerados vazios
     * FunÃ§Ã£o utilizada para remover os caracteres considerados vazios
     *
     * @param $value (misc) valor a ser substituido
     *
     * @returns (string) value
     *
     */
    public function removeSpaceChars($value)
    {
        $blanks = array(
            "\r" => '',
            "\t" => '',
            "\n" => '',
            '&nbsp;' => '',
            ' ' => ''
        );

        return strtr($value, $blanks);
    }

    /**
     * Retira os caracteres especiais.
     *
     * @param <type> $string
     */
    public static function RemoveSpecialCharsAndNumbers(
        $string,
        $whiteList = array()
    )
    {
        $stringWithoutSpecialChars = static::RemoveSpecialChars($string,
            $whiteList);
        return trim(preg_replace('/\d+/', '', $stringWithoutSpecialChars));
    }

    private static function getSpecialCharsExceptAccents()
    {
        return array(
            '#' => '',
            '$' => '',
            '%' => '',
            '&' => '',
            '@' => '',
            '.' => '',
            '?' => '',
            '+' => '',
            '=' => '',
            '§' => '',
            '-' => '',
            '\\' => '',
            '/' => '',
            '!' => '',
            '"' => '',
            "'" => '',
            '´' => '',
            '¿' => ''
        );
    }

    private static function getAccentedChars()
    {
        $arrayStringsSpecialChars = array(
            "À",
            "Á",
            "Â",
            "Ã",
            "Ä",
            "Å",
            "?",
            "á",
            "â",
            "ã",
            "ä",
            "å",
            "Ò",
            "Ó",
            "Ô",
            "Õ",
            "Ö",
            "Ø",
            "ò",
            "ó",
            "ô",
            "õ",
            "ö",
            "ø",
            "È",
            "É",
            "Ê",
            "Ë",
            "è",
            "é",
            "ê",
            "ë",
            "Ç",
            "ç",
            "Ì",
            "Í",
            "Î",
            "Ï",
            "ì",
            "í",
            "î",
            "ï",
            "Ù",
            "Ú",
            "Û",
            "Ü",
            "ù",
            "ú",
            "û",
            "ü",
            "ÿ",
            "Ñ",
            "ñ"
        );
        $arrayStringsNormalChars = array(
            "A",
            "A",
            "A",
            "A",
            "A",
            "A",
            "a",
            "a",
            "a",
            "a",
            "a",
            "a",
            "O",
            "O",
            "O",
            "O",
            "O",
            "O",
            "o",
            "o",
            "o",
            "o",
            "o",
            "o",
            "E",
            "E",
            "E",
            "E",
            "e",
            "e",
            "e",
            "e",
            "C",
            "c",
            "I",
            "I",
            "I",
            "I",
            "i",
            "i",
            "i",
            "i",
            "U",
            "U",
            "U",
            "U",
            "u",
            "u",
            "u",
            "u",
            "y",
            "N",
            "n"
        );

        return array_combine($arrayStringsSpecialChars,
            $arrayStringsNormalChars);
    }

    private static function replaceSpecialChars(
        $string,
        $replacementCharMap,
        $whiteList = array()
    )
    {
        while (list($character, $replacement) = each($replacementCharMap)) {
            if (!in_array($character, $whiteList)) {
                $string = str_replace($character, $replacement, $string);
            }
        }

        return $string;
    }

    public static function RemoveSpecialCharsExceptAccents(
        $string,
        $whiteList = array()
    )
    {
        return self::replaceSpecialChars($string,
            self::getSpecialCharsExceptAccents(), $whiteList);
    }

    /**
     * Retira os caracteres especiais.
     *
     * @param <type> $string
     */
    public static function RemoveSpecialChars($string, $whiteList = array())
    {
        $specialCharacters = array_merge(self::getSpecialCharsExceptAccents(),
            self::getAccentedChars());
        return self::replaceSpecialChars($string, $specialCharacters,
            $whiteList);
    }

    /**
     * @todo TRANSLATION
     * Copia diretorio
     * Esta funcao copia o conteudo de um diretorio para outro
     *
     * @param $sourceDir (string) Diretorio de origem
     * @param $destinDir (string) Diretorio de destino
     *
     * @returns (string) value
     */
    public function copyDirectory($sourceDir, $destinDir)
    {
        if (file_exists($sourceDir) && file_exists($destinDir)) {
            $open_dir = opendir($sourceDir);

            while (false !== ($file = readdir($open_dir))) {
                if ($file != "." && $file != "..") {
                    $aux = explode('.', $file);

                    if ($aux[0] != "") {
                        if (file_exists($destinDir . "/" . $file)
                            && filetype($destinDir . "/" . $file) != "dir"
                        ) {
                            unlink($destinDir . "/" . $file);
                        }
                        if (filetype($sourceDir . "/" . $file) == "dir") {
                            if (!file_exists($destinDir . "/" . $file)) {
                                mkdir($destinDir . "/" . $file . "/");
                                self::copyDirectory($sourceDir . "/" . $file,
                                    $destinDir . "/" . $file);
                            }
                        } else {
                            copy($sourceDir . "/" . $file,
                                $destinDir . "/" . $file);
                        }
                    }
                }
            }
        }
    }

    /**
     * @todo TRANSLATION
     * Remove diretorio
     * Esta funcao remove recursivamente o diretorio e todo o conteudo existente dentro dele
     *
     * @param $directory (string) Diretorio a ser removido
     * @param $empty (string)
     *
     * @returns (string) value
     */
    public function removeDirectory($directory, $empty = false)
    {
        if (substr($directory, -1) == '/') {
            $directory = substr($directory, 0, -1);
        }

        if (!file_exists($directory) || !is_dir($directory)) {
            return false;
        } elseif (is_readable($directory)) {
            $handle = opendir($directory);

            while (false !== ($item = readdir($handle))) {
                if ($item != '.' && $item != '..') {
                    $path = $directory . '/' . $item;

                    if (is_dir($path)) {
                        self::removeDirectory($path);
                    } else {
                        unlink($path);
                    }
                }
            }

            closedir($handle);

            if ($empty == false) {
                if (!rmdir($directory)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @todo TRANSLATION
     * Retorna o diretÃ³rio temporario
     * Esta funcao retorna o diretÃ³rio temporÃ¡rio do sistema operacional
     *
     * @returns (string) directory name
     */
    static public function getSystemTempDir()
    {
        $tempFile = tempnam(md5(uniqid(rand(), true)), '');
        if ($tempFile) {
            $tempDir = realpath(dirname($tempFile));
            unlink($tempFile);

            return $tempDir;
        } else {
            return '/tmp';
        }
    }

    function getMemUsage()
    {

        if (function_exists('memory_get_usage')) {
            return memory_get_usage();
        } else {
            if (substr(PHP_OS, 0, 3) == 'WIN') {
                // Windows 2000 workaround

                $output = array();
                exec('pslist ' . getmypid(), $output);
                return trim(substr($output[8], 38, 10));
            } else {
                return '<b style="color: red;">no value</b>';
            }
        }
    }

    function unix2dos($arquivo)
    {
        $file = file("$arquivo");
        $conteudo = "";
        foreach ($file as $texto) {
            $conteudo .= substr($texto, 0, -1) . "\r\n";
        }
        if (is_writable($arquivo)) {
            $manipular = fopen("$arquivo", "w");
            fwrite($manipular, $conteudo);
            fclose($manipular);
        } else {
            throw new EControlException("O arquivo: \"$arquivo\"  n&atilde;o possui permiss&otilde;es de leitura/escrita.");
        }
    }

    public static function formatValue($value, $precision = 2)
    {
        return number_format($value, $precision, ',', '.');
    }

    public static function invertDate($date)
    {
        $mdate = new MDate($date);
        return $mdate->invert();
    }

    public static function getDateTimeFromNow($seconds, $format = 'd/m/Y H:i:s')
    {
        return date($format, strtotime("$seconds second"));
    }

    /**
     * Searches the array recursively for a given value and returns the corresponding key if successful.
     *
     * @param  (string) $needle
     * @param  (array) $haystack
     *
     * @return (mixed) If found, returns the key, othreways FALSE.
     */
    public static function arraySearchRecursive($needle, $haystack)
    {
        $found = false;
        $result = false;

        foreach ($haystack as $k => $v) {
            if (is_array($v)) {
                for ($i = 0; $i < count($v); $i++) {
                    if ($v[$i] === $needle) {
                        $result = $v[0];
                        $found = true;
                        break;
                    }
                }
            } else {
                if ($found = ($v === $needle)) {
                    $result = $k;
                }
            }

            if ($found == true) {
                break;
            }
        }

        return $result;
    }

    /**
     * Função para ordenar um array de array por ordem das colunas passadas em um array
     *
     * @param array $vetor : array de array que desejo ordernar
     *                     exemplo: $vetor = array(array('a', 'b', 'c'), array('d', 'e', 'f'),...)
     * @param array $order : array com ordem das colunas de ordenação
     *                     exemplo: $vetor = array(1,5,17,2,65,0)
     *                     * @return array
     */
    public static function orderArrayByArray(array $vetor, array $order)
    {
        usort($vetor, function ($a, $b) use ($order) {
            for ($i = 0; $i < count($order); $i++) {
                $comp = strcasecmp($a[$order[$i]], $b[$order[$i]]);
                if ($comp != 0) {
                    break;
                }
            }
            return $comp;
        });
        return $vetor;
    }

    /**
     * Return an array of (or one, if indicated)  MFile objects from $_FILES
     * $files => $_FILES
     */
    public static function parseFiles($id, $index = null)
    {
        $array = array();
        if (count($_FILES)) {
            foreach ($_FILES as $var => $file) {
                if (strpos($var, $id) !== false) {
                    if (is_array($file['name'])) {
                        $n = count($file['name']);
                        $f = array();
                        for ($i = 0; $i < $n; $i++) {
                            if ($file['size'][$i] > 0) {
                                $f['name'] = $file['name'][$i];
                                $f['type'] = $file['type'][$i];
                                $f['tmp_name'] = $file['tmp_name'][$i];
                                $f['error'] = $file['error'][$i];
                                $f['size'] = $file['size'][$i];
                                $array[] = new MFile($f);
                            }
                        }
                    } else {
                        if ($file['size'] > 0) {
                            $array[] = new MFile($file);
                        }
                    }
                }
            }
        }
        if (count($array)) {
            return ($index !== null ? $array[$index] : $array);
        } else {
            return null;
        }
    }

    public static function arrayColumn($array, $key, $insert = null)
    {
        if (is_array($key) || !is_array($array)) {
            return $array;
        }
        if (is_null($insert)) {
            $func = create_function('$e',
                'return is_array($e) && array_key_exists("' . $key
                . '",$e) ? $e["' . $key . '"] : null;');
            return array_map($func, $array);
        } else {
            $return = array();
            foreach ($array as $i => $row) {
                $return[$i] = $row ?: array();
                $return[$i][$key] = $insert[$i];
            }
            return $return;
        }
    }

    public static function arrayTree($array, $group, $node)
    {
        $tree = array();
        if ($rs = $array) {
            $node = explode(',', $node);
            $group = explode(',', $group);
            foreach ($rs as $row) {
                $aNode = array();
                foreach ($node as $n) {
                    $aNode[] = $row[$n];
                }
                $s = '';
                foreach ($group as $g) {
                    $s .= "[" . $row[$g] . "]";
                }
                eval("\$tree{$s}" . "[] = \$aNode;");
            }
        }
        return $tree;
    }

    /**
     * Adds a record at the beginning of the array.
     *
     * @param  (array) $array
     * @param  (mixed) $chave
     * @param  (mixed) $valor
     *
     * @return (array) $array
     */
    public static function arrayInsert($array, $chave = null, $valor = null)
    {
        $array = array_reverse($array, true);
        $array[$chave] = $valor;
        return array_reverse($array, true);
    }

    public static function parseArray($value)
    {
        if (!is_array($value)) {
            $value = array($value);
        }
        return $value;
    }

    public static function arrayMergeOverwrite($arr1, $arr2)
    {
        foreach ($arr2 as $key => $value) {
            if (array_key_exists($key, $arr1) && is_array($value)) {
                $arr1[$key] = MUtil::arrayMergeOverwrite($arr1[$key],
                    $arr2[$key]);
            } else {
                $arr1[$key] = $value;
            }
        }
        return $arr1;
    }

    public static function detectUTF8($string)
    {
        return preg_match('%(?:
        [\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
        |\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
        |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
        |\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
        |\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
        |[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
        |\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
        )+%xs', $string);
    }

    public static function roundBetter($amount, $precision, $direction = 'down')
    {
        $cf = new MCurrencyFormatter();

        $amount = $cf->toDecimal($amount);
        $factor = pow(10, $precision);
        $mult = $amount * $factor;
        $mult = $cf->toDecimal("$mult");

        return ((strtolower($direction) == 'down') ? floor($mult) : ceil($mult))
            / $factor;
    }

    public static function roundDown($amount, $precision)
    {
        return self::roundBetter($amount, $precision, 'down');
    }

    public static function roundUp($amount, $precision)
    {
        return self::roundBetter($amount, $precision, 'up');
    }

    public static function upper($value)
    {
        $charset = \Manager::getConf("options.charset");
        return mb_strtoupper($value, $charset);
    }

    /**
     * Função para verificar se todos os atributos de um objeto são nulos
     *
     * @param type $object
     *
     * @return true se o objeto tiver todos atributos nulos | false se, pelo menos, um atributo do objeto não for nulo
     */
    public static function isObjectNull($object)
    {
        $isNull = true;
        $arrayObject = get_object_vars($object);
        foreach ($arrayObject as $key) {
            if ($key != null) {
                $isNull = false;
                break;
            }
        }
        return $isNull;
    }

    /**
     * Verifica se um endereço de e-mail é válido
     *
     * @param string $email
     *
     * @return boolean
     */
    public static function isEmailValido($email)
    {
        $regex = '/^[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4}$/';
        return preg_match($regex, $email);
    }

    /**
     * Recursive function to get an associative array of class properties by property name => ReflectionProperty() object
     * including inherited ones from extended classes
     *
     * @param string $className Class name
     * @param string $types Any combination of <b>public, private, protected, static</b>
     *
     * @return array
     */
    public static function getClassProperties($className, $types = 'public')
    {
        $ref = new \ReflectionClass($className);
        $props = $ref->getProperties();
        $props_arr = array();
        foreach ($props as $prop) {
            $f = $prop->getName();
            if ($prop->isPublic() and (stripos($types, 'public') === false)) {
                continue;
            }
            if ($prop->isPrivate() and (stripos($types, 'private') === false)) {
                continue;
            }
            if ($prop->isProtected() and (stripos($types, 'protected')
                    === false)) {
                continue;
            }
            if ($prop->isStatic() and (stripos($types, 'static') === false)) {
                continue;
            }
            $props_arr[$f] = $prop;
        }
        if ($parentClass = $ref->getParentClass()) {
            $parent_props_arr
                = MUtil::getClassProperties($parentClass->getName(),
                $types);//RECURSION
            if (count($parent_props_arr) > 0) {
                $props_arr = array_merge($parent_props_arr, $props_arr);
            }
        }
        return $props_arr;
    }

    public static function generateUID()
    {
        $s = uniqid('', true);
        $hex = substr($s, 0, 13);
        $dec = $s[13] . substr($s, 15); // skip the dot

        $uid = base_convert($hex, 16, 36) . base_convert($dec, 10, 36);
        return strtoupper($uid);
    }

    /**
     * Gera uma cadeia de bytes de forma randômica e a converte para uma string em formato hexadecimal.
     *
     * @param Integer $length Tamanho em bytes da cadeia a ser gerada.
     *
     * @return String Cadeia de bytes convertida para string em formato hexadecimal.
     * @link http://php.net/manual/en/function.openssl-random-pseudo-bytes.php
     */
    public static function generateRandomBytesAsString($length)
    {
        if ((!$length) || (!is_numeric($length)) || ($length <= 0)) {
            throw new InvalidArgumentException("Invalid length. The length must be greater than 0.");
        }
        return bin2hex(openssl_random_pseudo_bytes($length));
    }

    public static function getClientIP()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $ip = getenv('HTTP_X_FORWARDED_FOR');
            } else {
                if (getenv('HTTP_X_FORWARDED')) {
                    $ip = getenv('HTTP_X_FORWARDED');
                } else {
                    if (getenv('HTTP_FORWARDED_FOR')) {
                        $ip = getenv('HTTP_FORWARDED_FOR');
                    } else {
                        if (getenv('HTTP_FORWARDED')) {
                            $ip = getenv('HTTP_FORWARDED');
                        } else {
                            if (getenv('REMOTE_ADDR')) {
                                $ip = getenv('REMOTE_ADDR');
                            } else {
                                $ip = 'UNKNOWN';
                            }
                        }
                    }
                }
            }
        }

        return $ip;
    }

    /**
     * Pega a hora corrente em milisegundos.
     *
     * @return float Hora corrente em milisegundos.
     */
    public static function getCurrentTimeInMilliseconds()
    {
        return round(microtime(true) * 1000);
    }

    /**
     * Gera valores booleanos de acordo com a probabilidade informada.
     *
     * @param float $trueChance Probabilidade de gerar um true;
     *
     * @return bool
     * @throws Exception
     */
    public static function randomBoolean($trueChance = 0.5)
    {
        if ($trueChance < 0.00001 || $trueChance > 1) {
            throw new \Exception("Valor inválido");
        }

        $max = 100000;
        return (mt_rand(0, $max) <= $trueChance * $max);
    }

    /**
     *
     * @param $value
     *
     * @return null|string
     */
    public static function zeroOrOneToSorN($value)
    {
        switch ($value) {
            case 0;
                return 'N';
            case 1:
                return 'S';
            default:
                return null;
        }
    }

    public static function sendMail($address, $subject, $body)
    {
        $mail = \MMailer::getMailer(null);
        $mail->addAddress($address);
        $mail->Subject = '[SIGA/UFJF] ' . $subject;
        $mail->Body = $body;
        return $mail->send();
    }

    public static function limitText($text, $charLimit)
    {
        if (mb_strlen($text) > $charLimit) {
            $text = mb_substr($text, 0, $charLimit - 3) . "...";
        }

        return $text;
    }

    public static function abbreviateAllWords($input, $limit)
    {
        $words = explode(' ', $input);
        $output = '';

        foreach ($words as $word) {
            if (mb_strlen($word) > $limit) {
                $word = mb_substr($word, 0, $limit) . '.';
            }

            $output .= $word . ' ';
        }

        return trim($output);
    }

    /**
     * Verifica se o array está vazio.
     *
     * @param $arrayToCheck array a ser verificada.
     *
     * @return bool Resultado da verificação.
     */
    public static function isEmptyArray($arrayToCheck)
    {
        if (!$arrayToCheck) {
            return true;
        }

        if (!is_array($arrayToCheck)) {
            return true;
        }

        if (count($arrayToCheck) == 0) {
            return true;
        }
        return false;
    }

    public static function isAssoc($input)
    {
        if (is_object($input)) return true;
        if (array() === $input) return false;
        return array_keys($input) !== range(0, count($input) - 1);
    }

    public static function php2js($input, $sequential_keys = false, $quotes = false, $beautiful_json = false)
    {
        $output = self::isAssoc($input) ? "{" : "[";
        $count = 0;
        if (is_object($input)) {
            $arrayobj = new ArrayObject($input);
            $n = $arrayobj->count();
        } else {
            $n = count($input);
        }
        foreach ($input as $key => $value) {
            if (self::isAssoc($input) || (!self::isAssoc($input) && $sequential_keys == true)) {
                $output .= ($quotes ? '"' : '') . $key . ($quotes ? '"' : '') . ' : ';
            }
            if (is_array($value) || is_object($value)) {
                $output .= self::php2js($value, $sequential_keys, $quotes, $beautiful_json);
            } else if (is_bool($value)) {
                $output .= ($value ? 'true' : 'false');
            } else if (is_numeric($value)) {
                $output .= $value;
            } else {
                //$output .= ($quotes || $beautiful_json ? '"' : '') . $value . ($quotes || $beautiful_json ? '"' : '');
                $output .= '"' . $value . '"';
            }
            if (++$count < $n) {
                $output .= ', ';
            }
        }
        $output .= self::isAssoc($input) ? "}" : "]";
        return $output;
    }

}

class MDummy
{

}

class MDataObject
{

}

?>
