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
 * Brief Class Description.
 * Complete Class Description.
 */
class MResponse
{

    private $mimeType = array(
        'ai' => 'application/postscript', 'aif' => 'audio/x-aiff',
        'aifc' => 'audio/x-aiff', 'aiff' => 'audio/x-aiff',
        'asf' => 'video/x-ms-asf', 'asr' => 'video/x-ms-asf',
        'asx' => 'video/x-ms-asf', 'au' => 'audio/basic',
        'avi' => 'video/x-msvideo', 'bin' => 'application/octet-stream',
        'bmp' => 'image/bmp', 'css' => 'text/css',
        'doc' => 'application/msword', 'gif' => 'image/gif',
        'gz' => 'application/x-gzip', 'hlp' => ' application/winhlp',
        'htm' => 'text/html', 'html' => 'text/html',
        'ico' => 'image/x-icon', 'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg', 'jpg' => 'image/jpeg',
        'js' => 'application/x-javascript', 'lzh' => 'application/octet-stream',
        'mid' => 'audio/mid', 'mov' => 'video/quicktime',
        'mp3' => 'audio/mpeg', 'mpa' => 'video/mpeg',
        'mpe' => 'video/mpeg', 'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg', 'pdf' => 'application/pdf',
        'png' => 'image/png', 'pps' => 'application/vnd.ms-powerpoint',
        'ppt' => 'application/vnd.ms-powerpoint', 'ps' => 'application/postscript',
        'qt' => 'video/quicktime', 'ra' => 'audio/x-pn-realaudio',
        'ram' => 'audio/x-pn-realaudio', 'rtf' => 'application/rtf',
        'snd' => 'audio/basic', 'tgz' => 'application/x-compressed',
        'tif' => 'image/tiff', 'tiff' => 'image/tiff',
        'txt' => 'text/plain', 'wav' => 'audio/x-wav',
        'xbm' => 'image/x-xbitmap', 'xpm' => 'image/x-xpixmap',
        'z' => 'application/x-compress', 'zip' => 'application/zip',
        'json' => 'application/json'
    );
    private $contentLength;
    private $contentDisposition;
    private $contentTransferEncoding;
    private $fileName;
    private $fileNameDown;
    private $baseName;
    private $alreadyFlushed = false;

    /**
     * Response status code
     */
    public $status = 200;

    /**
     * Response content type
     */
    public $contentType;

    /**
     * Response headers
     */
    public $headers;

    /**
     * Response cookies
     */
    public $cookies;

    /**
     * Response body stream
     */
    public $out;

    /**
     * Send this file directly
     */
    public $direct;


    public function __construct()
    {
        $this->contentType = "";
        $this->contentLength = "";
        $this->contentDisposition = "";
        $this->contentTransferEncoding = "";
        $this->fileName = "";
        $this->fileNameDown = "";
        $this->headers = new MStringList();
        $this->cookies = new MStringList();
    }

    /**
     * Get a response header
     * @param name Header name case-insensitive
     * @return the header value as a String
     */
    public function getHeader($name)
    {
        return $this->headers->get($name);
    }

    /**
     * Set a response header
     * @param name Header name
     * @param value Header value
     */
    public function setHeader($name, $value)
    {
        $this->headers->add($value, $name);
    }

    public function setContentTypeIfNotSet($contentType)
    {
        if ($this->contentType == '') {
            $this->contentType = $contentType;
        }
    }

    public function setOut($content)
    {
        $this->out = $content;
    }

    public function getOut()
    {
        return $this->out;
    }

    public function setStatus($value)
    {
        $this->status = $value;
    }

    public function getStatus()
    {
        return $this->value;
    }

    /**
     * Set a new cookie that will expire in (current) + duration
     * @param name
     * @param value
     * @param duration Ex: 3d
     */
    public function setCookie($name, $value, $expire = 0, $path = '', $domain = '', $secure = false, $httpOnly = false)
    {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }

    public function __down()
    {
        $this->contentType = "application/save";
        $this->contentLength = "";
        $this->contentDisposition = "";
        $this->contentTransferEncoding = "";
        $this->fileName = "";
        $this->fileNameDown = "";
    }

    public function setContentType($value)
    {
        $this->contentType = $value;
    }

    public function _setContentLength()
    {
        $this->contentLength = filesize($this->fileName);
    }

    public function setContentLength($value)
    {
        $this->contentLength = $value;
    }

    public function setContentDisposition($value)
    {
        $this->contentDisposition = $value;
    }

    public function setContentTransferEncoding($value)
    {
        $this->contentTransferEncoding = $value;
    }

    public function getMimeType($fileName)
    {
        $path_parts = pathinfo($fileName);
        $mime = $this->mimeType[$path_parts['extension']];
        $type = $mime ? $mime : "application/octet-stream";
        return $type;
    }

    /*
      Send methods.
     */

    /**
     * Send response to browser.
     * Analyse $result object and decide the method of response.
     * $return indicates if response is sent to browser ou returned to caller.
     *
     * @param object $result
     * @param boolean $return
     * @return string
     */
    public function sendResponse($result, $return = false)
    {
        if ($this->alreadyFlushed) {
            return;
        }
        if ($result == null) {
            return;
        }
        $request = Manager::getRequest();
        $response = $this;
        if ($result instanceof MRenderBinary) {
            $this->sendStream($result);
        } else {
            //mdump('%%% ' . get_class($result));
            $result->apply($request, $response);
            foreach ($this->headers->getItems() as $header) {
                header($header);
            }
            if ($return) {
                return $this->out;
            }
            $this->setResponseCode();
            echo $this->out;
        }
    }

    private function setResponseCode()
    {
        /* Em algumas situações, como falha de autenticação e erro interno ,
         * o código 200 não representa a situação real.  */
        if (http_response_code() == MStatusCode::OK) {
            http_response_code($this->status);
        }
    }

    public function sendStream($result)
    {
        $filePath = $result->getFilePath();
        if ($filePath != '') {
            if (file_exists($filePath)) {
                $fileName = $result->getFileName() ?: $this->baseName;
                $this->_setContentLength();
                header('Expires: 0');
                header('Pragma: public');
                header("Content-Type: " . $this->contentType);
                header("Content-Length: " . filesize($filePath));
                if ($result->getInline()) {
                    header("Content-Disposition: inline; filename=" . $fileName);
                } else {
                    header("Content-Disposition: attachment; filename=" . $fileName);
                }
                header("Cache-Control: cache"); // HTTP/1.1
                header("Content-Transfer-Encoding: binary");

                $fp = fopen($filePath, "r");
                fpassthru($fp);
                fclose($fp);
            }
        } else {
            $fileName = $result->getFileName() ?: 'download';
            $stream = $result->getStream();
            if ($fileName != 'raw') {
                $this->contentLength = strlen($stream);
                header('Expires: 0');
                header('Pragma: public');
                header("Content-Type: " . $this->contentType);
                header("Content-Length: " . $this->contentLength);
                if ($result->getInline()) {
                    header("Content-Disposition: inline; filename=" . $fileName);
                } else {
                    header("Content-Disposition: attachment; filename=" . $fileName);
                }
                header("Cache-Control: cache"); // HTTP/1.1
                header("Content-Transfer-Encoding: binary");
            }
            echo $stream;
        }
        exit;
    }

    public function prepareFlush()
    {
        $this->alreadyFlushed = true;
        header("Cache-Control: no-cache");
        for ($i = 0; $i < ob_get_level(); $i++) {
            ob_end_flush();
        }
        ob_implicit_flush(1);
        ob_start();
        echo str_repeat(" ", 1024), "\n";
    }

    public function sendFlush($output)
    {
        echo $output;
        ob_end_flush();
        ob_flush();
        flush();
    }

}
