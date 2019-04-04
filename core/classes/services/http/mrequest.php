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
class MRequest
{

    /**
     * Server host
     */
    public $host;
    /**
     * Request path
     */
    public $path;
    /**
     * QueryString
     */
    public $querystring;
    /**
     * Full url
     */
    public $url;
    /**
     * Base URL as set at configuration
     */
    public $baseUrl;
    /**
     * HTTP method
     */
    public $method;
    /**
     * Server domain
     */
    public $domain;
    /**
     * Client address
     */
    public $remoteAddress;
    /**
     * Request content-type
     */
    public $contentType;
    /**
     * HTTP port
     */
    public $port;
    /**
     * is HTTPS ?
     */
    public $secure = false;
    /**
     * HTTP Headers
     */
    public $headers;
    /**
     * HTTP Cookies
     */
    public $cookies;
    /**
     * Body stream
     */
    public $body;
    /**
     * Additional HTTP params extracted from route
     */
    public $routeArgs;
    /**
     * Format (html,xml,json,text)
     */
    public $format = null;
    /**
     * Free space to store your request specific data
     */
    public $args = array();
    /**
     * When the request has been received
     */
    public $date;
    /**
     * New request or already submitted
     */
    public $isNew = true;
    /**
     * HTTP Basic User
     */
    public $user;
    /**
     * HTTP Basic Password
     */
    public $password;
    /**
     * Request comes from loopback interface
     */
    public $isLoopback;
    /**
     * Params
     */
    public $params;
    /**
     * Dispatcher
     */
    public $dispatch;
    /**
     * Script Name
     */
    public $script;
    private $_requestUri;
    private $_pathInfo;
    private $_scriptFile;
    private $_scriptUrl;
    private $_hostInfo;
    private $_baseUrl;
    private $_cookies;
    private $_preferredLanguage;
    private $_csrfToken;
    private $_deleteParams;
    private $_putParams;


    public function __construct()
    {
        $this->host = $_SERVER['SERVER_NAME'];
        if ($this->host != '') {
            $this->path = $this->getPathInfo();
            mtrace('MRequest path = ' . $this->path);
            $this->querystring = $this->getQueryString();
            $this->method = $this->getRequestType();
            $this->domain = $this->getServerName();
            $this->remoteAddress = $this->getUserHostAddress();
            $this->processContentType();
            $this->port = $this->getPort();
            $this->secure = $this->getIsSecureConnection();
            $this->headers = $_SERVER;
            $this->cookies = isset($_COOKIES) ? $_COOKIES : '';
            $dispatch = Manager::getOptions('dispatch');
            $this->baseUrl = $this->getBaseUrl();
            $this->date = Manager::getSysTime();
            $this->isNew = true;
            $this->user = '';
            $this->password = '';
            $this->isLoopback = ($this->remoteAddress == '127.0.0.1');
            $this->params = $_REQUEST;
            $this->url = $this->getUrl();
            $this->dispatch = $this->getBase() . $dispatch;
            $this->resolveFormat();
            $auth = isset($_SERVER['AUTH_TYPE']) ? $_SERVER['AUTH_TYPE'] : '';
            if (($auth != '') && (substr($auth, 0, 6) == "Basic ")) {
                $this->user = $_SERVER['PHP_AUTH_USER'];
                $this->password = $_SERVER['PHP_AUTH_PW'];
            }
        } else {
            mtrace('MRequest: no server, maybe running offfile');
        }
    }

    /**
     * Automatically resolve request format from the Accept header
     * (in this order : html > xml > json > text)
     */
    public function resolveFormat()
    {

        if ($this->format != null) {
            return;
        }

        $accept = $_SERVER['HTTP_ACCEPT'];

        if ($accept == '') {
            $this->format = "html";
            return;
        }

        if ((strpos($accept, "application/xhtml") !== false) || (strpos($accept, "text/html") !== false) ){
            $this->format = "html";
            return;
        }

        if ((strpos($accept, "application/xml") !== false) || (strpos($accept, "text/xml") !== false)) {
            $this->format = "xml";
            return;
        }

        if (strpos($accept, "text/plain") !== false) {
            $this->format = "txt";
            return;
        }

        if (strpos($accept, "application/json") !== false || strpos($accept, "text/javascript") != false) {
            $this->format = "json";
            return;
        }

        if (substr($accept, 0, -3) == "*/*") {
            $this->format = "html";
            return;
        }

        $this->format = "html";
        return;
    }

    public function getFormat() {
        return $this->format;
    }

    /**
     * This request was sent by an Ajax framework.
     * (rely on the X-Requested-With header).
     */
    public function isAjax()
    {
        if ($this->isFileUpload()) {
            return true;
        }
        if ($_SERVER['HTTP_X_REQUESTED_WITH'] == '') {
            return false;
        }
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest");
    }

    public function isAjaxEvent()
    {
        if ($_SERVER['__ISAJAXEVENT'] == 'yes') {
            return false;
        }
    }

    public function isPostBack()
    {
        return $this->method == 'POST';
    }

    public function isFileUpload()
    {
        return ($_REQUEST['__ISFILEUPLOAD'] == 'yes');
    }

    public function isPage()
    {
        return $this->isPage;
    }

    public function getForm()
    {
        $form = $this->params['__FORM'];
        return $form ? $form : 'MainForm';
    }

    public function getParameters()
    {
        return $this->params;
    }

    public function getParameter($name)
    {
        return $this->params[$name];
    }

    public function getParametersNames()
    {
        return array_keys($this->params);
    }

    public function getParameterValues($name)
    {
        return $this->params[$name];
    }

    public function getURI()
    {
        return $_SERVER['REQUEST_URI'];
    }

    public function inDomain()
    {
        $url = $this->getBase();
        return ($url == $this->host);
    }

    public function getBase()
    {
        return $this->getBaseUrl();
        /*
          if ($this->port == 80 || $this->port == 443) {
          return ($this->secure ? "https" : "http") . '://' .  $this->domain . $this->baseUrl;
          }
          return ($this->secure ? "https" : "http") . '://' . $this->domain . ':' . $this->port . '/' . $this->baseUrl;
         *
         */
    }

    /**
     * Returns the named GET or POST parameter value.
     * If the GET or POST parameter does not exist, the second parameter to this method will be returned.
     * If both GET and POST contains such a named parameter, the GET parameter takes precedence.
     * @param string $name the GET parameter name
     * @param mixed $defaultValue the default parameter value if the GET parameter does not exist.
     * @return mixed the GET parameter value
     * @since 1.0.4
     * @see getQuery
     * @see getPost
     */
    public function getParam($name, $defaultValue = null)
    {
        return isset($_GET[$name]) ? $_GET[$name] : (isset($_POST[$name]) ? $_POST[$name] : $defaultValue);
    }

    /**
     * Returns the named GET parameter value.
     * If the GET parameter does not exist, the second parameter to this method will be returned.
     * @param string $name the GET parameter name
     * @param mixed $defaultValue the default parameter value if the GET parameter does not exist.
     * @return mixed the GET parameter value
     * @since 1.0.4
     * @see getPost
     * @see getParam
     */
    public function getQuery($name, $defaultValue = null)
    {
        return isset($_GET[$name]) ? $_GET[$name] : $defaultValue;
    }

    /**
     * Returns the named POST parameter value.
     * If the POST parameter does not exist, the second parameter to this method will be returned.
     * @param string $name the POST parameter name
     * @param mixed $defaultValue the default parameter value if the POST parameter does not exist.
     * @return mixed the POST parameter value
     * @since 1.0.4
     * @see getParam
     * @see getQuery
     */
    public function getPost($name, $defaultValue = null)
    {
        return isset($_POST[$name]) ? $_POST[$name] : $defaultValue;
    }

    /**
     * Returns the named DELETE parameter value.
     * If the DELETE parameter does not exist or if the current request is not a DELETE request,
     * the second parameter to this method will be returned.
     * @param string $name the DELETE parameter name
     * @param mixed $defaultValue the default parameter value if the DELETE parameter does not exist.
     * @return mixed the DELETE parameter value
     * @since 1.1.7
     */
    public function getDelete($name, $defaultValue = null)
    {
        if ($this->_deleteParams === null)
            $this->_deleteParams = $this->getIsDeleteRequest() ? $this->getRestParams() : array();
        return isset($this->_deleteParams[$name]) ? $this->_deleteParams[$name] : $defaultValue;
    }

    /**
     * Returns the named PUT parameter value.
     * If the PUT parameter does not exist or if the current request is not a PUT request,
     * the second parameter to this method will be returned.
     * @param string $name the PUT parameter name
     * @param mixed $defaultValue the default parameter value if the PUT parameter does not exist.
     * @return mixed the PUT parameter value
     * @since 1.1.7
     */
    public function getPut($name, $defaultValue = null)
    {
        if ($this->_putParams === null)
            $this->_putParams = $this->getIsPutRequest() ? $this->getRestParams() : array();
        return isset($this->_putParams[$name]) ? $this->_putParams[$name] : $defaultValue;
    }

    /**
     * Returns the PUT or DELETE request parameters.
     * @return array the request parameters
     * @since 1.1.7
     */
    protected function getRestParams()
    {
        $result = array();
        if (function_exists('mb_parse_str'))
            mb_parse_str(file_get_contents('php://input'), $result);
        else
            parse_str(file_get_contents('php://input'), $result);
        return $result;
    }

    /**
     * Returns the currently requested URL.
     * This is the same as {@link getRequestUri}.
     * @return string part of the request URL after the host info.
     */
    public function getUrl()
    {
        return $this->getRequestUri();
    }

    /**
     * Returns the schema and host part of the application URL.
     * The returned URL does not have an ending slash.
     * By default this is determined based on the user request information.
     * You may explicitly specify it by setting the {@link setHostInfo hostInfo} property.
     * @param string $schema schema to use (e.g. http, https). If empty, the schema used for the current request will be used.
     * @return string schema and hostname part (with port number if needed) of the request URL (e.g. http://www.yiiframework.com)
     * @see setHostInfo
     */
    public function getHostInfo($schema = '')
    {
        if ($this->_hostInfo === null) {
            if ($secure = $this->getIsSecureConnection())
                $http = 'https';
            else
                $http = 'http';
            if (isset($_SERVER['HTTP_HOST']))
                $this->_hostInfo = $http . '://' . $_SERVER['HTTP_HOST'];
            else {
                $this->_hostInfo = $http . '://' . $_SERVER['SERVER_NAME'];
                $port = $secure ? $this->getSecurePort() : $this->getPort();
                if (($port !== 80 && !$secure) || ($port !== 443 && $secure))
                    $this->_hostInfo .= ':' . $port;
            }
        }
        if ($schema !== '') {
            $secure = $this->getIsSecureConnection();
            if ($secure && $schema === 'https' || !$secure && $schema === 'http')
                return $this->_hostInfo;

            $port = $schema === 'https' ? $this->getSecurePort() : $this->getPort();
            if ($port !== 80 && $schema === 'http' || $port !== 443 && $schema === 'https')
                $port = ':' . $port;
            else
                $port = '';

            $pos = strpos($this->_hostInfo, ':');
            return $schema . substr($this->_hostInfo, $pos, strcspn($this->_hostInfo, ':', $pos + 1) + 1) . $port;
        } else
            return $this->_hostInfo;
    }

    /**
     * Sets the schema and host part of the application URL.
     * This setter is provided in case the schema and hostname cannot be determined
     * on certain Web servers.
     * @param string $value the schema and host part of the application URL.
     */
    public function setHostInfo($value)
    {
        $this->_hostInfo = rtrim($value, '/');
    }

    /**
     * Returns the relative URL for the application.
     * This is similar to {@link getScriptUrl scriptUrl} except that
     * it does not have the script file name, and the ending slashes are stripped off.
     * @param boolean $absolute whether to return an absolute URL. Defaults to false, meaning returning a relative one.
     * This parameter has been available since 1.0.2.
     * @return string the relative URL for the application
     * @see setScriptUrl
     */
    public function getBaseUrl($absolute = false)
    {
        if ($this->_baseUrl === null)
            $this->_baseUrl = rtrim(dirname($this->getScriptUrl()), '\\/');
        return $absolute ? $this->getHostInfo() . $this->_baseUrl : $this->_baseUrl;
    }

    /**
     * Sets the relative URL for the application.
     * By default the URL is determined based on the entry script URL.
     * This setter is provided in case you want to change this behavior.
     * @param string $value the relative URL for the application
     */
    public function setBaseUrl($value)
    {
        $this->_baseUrl = $value;
    }

    /**
     * Returns the relative URL of the entry script.
     * The implementation of this method referenced Zend_Controller_Request_Http in Zend Framework.
     * @return string the relative URL of the entry script.
     */
    public function getScriptUrl()
    {
        if ($this->_scriptUrl === null) {
            $scriptName = basename($_SERVER['SCRIPT_FILENAME']);
            if (basename($_SERVER['SCRIPT_NAME']) === $scriptName)
                $this->_scriptUrl = $_SERVER['SCRIPT_NAME'];
            else if (basename($_SERVER['PHP_SELF']) === $scriptName)
                $this->_scriptUrl = $_SERVER['PHP_SELF'];
            else if (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $scriptName)
                $this->_scriptUrl = $_SERVER['ORIG_SCRIPT_NAME'];
            else if (($pos = strpos($_SERVER['PHP_SELF'], '/' . $scriptName)) !== false)
                $this->_scriptUrl = substr($_SERVER['SCRIPT_NAME'], 0, $pos) . '/' . $scriptName;
            else if (isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) === 0)
                $this->_scriptUrl = str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']));
            else
                throw new Exception('MRequest is unable to determine the entry script URL.');
        }
        return $this->_scriptUrl;
    }

    /**
     * Sets the relative URL for the application entry script.
     * This setter is provided in case the entry script URL cannot be determined
     * on certain Web servers.
     * @param string $value the relative URL for the application entry script.
     */
    public function setScriptUrl($value)
    {
        $this->_scriptUrl = '/' . trim($value, '/');
    }

    /**
     * Returns the path info of the currently requested URL.
     * This refers to the part that is after the entry script and before the question mark.
     * The starting and ending slashes are stripped off.
     * @return string part of the request URL that is after the entry script and before the question mark.
     * Note, the returned pathinfo is decoded starting from 1.1.4.
     * Prior to 1.1.4, whether it is decoded or not depends on the server configuration
     * (in most cases it is not decoded).
     * @throws CException if the request URI cannot be determined due to improper server configuration
     */
    public function getPathInfo()
    {
        if ($this->_pathInfo === null) {
            $pathInfo = $this->getRequestUri();
            if (($pos = strpos($pathInfo, '?')) !== false)
                $pathInfo = substr($pathInfo, 0, $pos);

            $pathInfo = urldecode($pathInfo);
            $scriptUrl = $this->getScriptUrl();
            $baseUrl = $this->getBaseUrl();
            if (strpos($pathInfo, $scriptUrl) === 0)
                $pathInfo = substr($pathInfo, strlen($scriptUrl));
            else if ($baseUrl === '' || strpos($pathInfo, $baseUrl) === 0)
                $pathInfo = substr($pathInfo, strlen($baseUrl));
            else if (strpos($_SERVER['PHP_SELF'], $scriptUrl) === 0)
                $pathInfo = substr($_SERVER['PHP_SELF'], strlen($scriptUrl));
            else
                throw new Exception('MRequest is unable to determine the path info of the request.');

            $this->_pathInfo = trim($pathInfo, '/');
        }
        return $this->_pathInfo;
    }

    public function setPathInfo($value)
    {
        $this->_pathInfo = trim($value);
    }

    /**
     * Returns the request URI portion for the currently requested URL.
     * This refers to the portion that is after the {@link hostInfo host info} part.
     * It includes the {@link queryString query string} part if any.
     * The implementation of this method referenced Zend_Controller_Request_Http in Zend Framework.
     * @return string the request URI portion for the currently requested URL.
     * @throws CException if the request URI cannot be determined due to improper server configuration
     * @since 1.0.1
     */
    public function getRequestUri()
    {
        if ($this->_requestUri === null) {
            if ($_SERVER["SERVER_SOFTWARE"] == "JavaBridge")
                $this->_requestUri = $_SERVER['PHP_SELF'];
            else if (isset($_SERVER['HTTP_X_REWRITE_URL'])) // IIS
                $this->_requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
            else if (isset($_SERVER['REQUEST_URI'])) {
                $this->_requestUri = $_SERVER['REQUEST_URI'];
                if (isset($_SERVER['HTTP_HOST'])) {
                    if (strpos($this->_requestUri, $_SERVER['HTTP_HOST']) !== false)
                        $this->_requestUri = preg_replace('/^\w+:\/\/[^\/]+/', '', $this->_requestUri);
                } else
                    $this->_requestUri = preg_replace('/^(http|https):\/\/[^\/]+/i', '', $this->_requestUri);
            } else if (isset($_SERVER['ORIG_PATH_INFO'])) {  // IIS 5.0 CGI
                $this->_requestUri = $_SERVER['ORIG_PATH_INFO'];
                if (!empty($_SERVER['QUERY_STRING']))
                    $this->_requestUri .= '?' . $_SERVER['QUERY_STRING'];
            } else
                throw new Exception('MRequest is unable to determine the request URI.');
        }
        return $this->_requestUri;
    }

    /**
     * Returns part of the request URL that is after the question mark.
     * @return string part of the request URL that is after the question mark
     */
    public function getQueryString()
    {
        return isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
    }

    /**
     * Return if the request is sent via secure channel (https).
     * @return boolean if the request is sent via secure channel (https)
     */
    public function getIsSecureConnection()
    {
        return isset($_SERVER['HTTPS']) && !strcasecmp($_SERVER['HTTPS'], 'on');
    }

    /**
     * Returns the request type, such as GET, POST, HEAD, PUT, DELETE.
     * @return string request type, such as GET, POST, HEAD, PUT, DELETE.
     */
    public function getRequestType()
    {
        return strtoupper(isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET');
    }

    /**
     * Returns the request type, such as GET, POST, HEAD, PUT, DELETE.
     * @return string request type, such as GET, POST, HEAD, PUT, DELETE.
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Returns whether this is a POST request.
     * @return boolean whether this is a POST request.
     */
    public function getIsPostRequest()
    {
        return isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'], 'POST');
    }

    /**
     * Returns whether this is a DELETE request.
     * @return boolean whether this is a DELETE request.
     * @since 1.1.7
     */
    public function getIsDeleteRequest()
    {
        return isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'], 'DELETE');
    }

    /**
     * Returns whether this is a PUT request.
     * @return boolean whether this is a PUT request.
     * @since 1.1.7
     */
    public function getIsPutRequest()
    {
        return isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'], 'PUT');
    }

    /**
     * Returns whether this is an AJAX (XMLHttpRequest) request.
     * @return boolean whether this is an AJAX (XMLHttpRequest) request.
     */
    public function getIsAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * Returns the server name.
     * @return string server name
     */
    public function getServerName()
    {
        return $_SERVER['SERVER_NAME'];
    }

    /**
     * Returns the server port number.
     * @return integer server port number
     */
    public function getServerPort()
    {
        return $_SERVER['SERVER_PORT'];
    }

    /**
     * Returns the URL referrer, null if not present
     * @return string URL referrer, null if not present
     */
    public function getUrlReferrer()
    {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
    }

    /**
     * Returns the user agent, null if not present.
     * @return string user agent, null if not present
     */
    public function getUserAgent()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
    }

    /**
     * Returns the user IP address.
     * @return string user IP address
     */
    public function getUserHostAddress()
    {
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
    }

    /**
     * Returns the user host name, null if it cannot be determined.
     * @return string user host name, null if cannot be determined
     */
    public function getUserHost()
    {
        return isset($_SERVER['REMOTE_HOST']) ? $_SERVER['REMOTE_HOST'] : null;
    }

    /**
     * Returns entry script file path.
     * @return string entry script file path (processed w/ realpath())
     */
    public function getScriptFile()
    {
        if ($this->_scriptFile !== null)
            return $this->_scriptFile;
        else
            return $this->_scriptFile = realpath($_SERVER['SCRIPT_FILENAME']);
    }

    /**
     * Returns information about the capabilities of user browser.
     * @param string $userAgent the user agent to be analyzed. Defaults to null, meaning using the
     * current User-Agent HTTP header information.
     * @return array user browser capabilities.
     * @see http://www.php.net/manual/en/function.get-browser.php
     */
    public function getBrowser($userAgent = null)
    {
        return get_browser($userAgent, true);
    }

    /**
     * Returns user browser accept types, null if not present.
     * @return string user browser accept types, null if not present
     */
    public function getAcceptTypes()
    {
        return isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : null;
    }

    private $_port;

    /**
     * Returns the port to use for insecure requests.
     * Defaults to 80, or the port specified by the server if the current
     * request is insecure.
     * You may explicitly specify it by setting the {@link setPort port} property.
     * @return integer port number for insecure requests.
     * @see setPort
     * @since 1.1.3
     */
    public function getPort()
    {
        if ($this->_port === null)
            $this->_port = !$this->getIsSecureConnection() && isset($_SERVER['SERVER_PORT']) ? (int)$_SERVER['SERVER_PORT'] : 80;
        return $this->_port;
    }

    /**
     * Sets the port to use for insecure requests.
     * This setter is provided in case a custom port is necessary for certain
     * server configurations.
     * @param integer $value port number.
     * @since 1.1.3
     */
    public function setPort($value)
    {
        $this->_port = (int)$value;
        $this->_hostInfo = null;
    }

    private $_securePort;

    /**
     * Returns the port to use for secure requests.
     * Defaults to 443, or the port specified by the server if the current
     * request is secure.
     * You may explicitly specify it by setting the {@link setSecurePort securePort} property.
     * @return integer port number for secure requests.
     * @see setSecurePort
     * @since 1.1.3
     */
    public function getSecurePort()
    {
        if ($this->_securePort === null)
            $this->_securePort = $this->getIsSecureConnection() && isset($_SERVER['SERVER_PORT']) ? (int)$_SERVER['SERVER_PORT'] : 443;
        return $this->_securePort;
    }

    /**
     * Sets the port to use for secure requests.
     * This setter is provided in case a custom port is necessary for certain
     * server configurations.
     * @param integer $value port number.
     * @since 1.1.3
     */
    public function setSecurePort($value)
    {
        $this->_securePort = (int)$value;
        $this->_hostInfo = null;
    }

    /**
     * Redirects the browser to the specified URL.
     * @param string $url URL to be redirected to. If the URL is a relative one, the base URL of
     * the application will be inserted at the beginning.
     * @param boolean $terminate whether to terminate the current application
     * @param integer $statusCode the HTTP status code. Defaults to 302. See {@link http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html}
     * for details about HTTP status code. This parameter has been available since version 1.0.4.
     */
    public function redirect($url, $terminate = true, $statusCode = 302)
    {
        if (strpos($url, '/') === 0)
            $url = $this->getHostInfo() . $url;
        header('Location: ' . $url, true, $statusCode);
        if ($terminate)
            Yii::app()->end();
    }

    /**
     * Returns the user preferred language.
     * The returned language ID will be canonicalized using {@link CLocale::getCanonicalID}.
     * This method returns false if the user does not have language preference.
     * @return string the user preferred language.
     */
    public function getPreferredLanguage()
    {
        if ($this->_preferredLanguage === null) {
            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && ($n = preg_match_all('/([\w\-_]+)\s*(;\s*q\s*=\s*(\d*\.\d*))?/', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches)) > 0) {
                $languages = array();
                for ($i = 0; $i < $n; ++$i)
                    $languages[$matches[1][$i]] = empty($matches[3][$i]) ? 1.0 : floatval($matches[3][$i]);
                arsort($languages);
                foreach ($languages as $language => $pref)
                    return $this->_preferredLanguage = CLocale::getCanonicalID($language);
            }
            return $this->_preferredLanguage = false;
        }
        return $this->_preferredLanguage;
    }

    public function getContentType() {
        return $this->contentType;
    }

    private function processContentType() {
        $this->contentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
        if (strpos($this->contentType, "application/json") !== false) {
            if ($this->getMethod() == 'POST')
            {
                $data = json_decode(file_get_contents("php://input"));
                $_REQUEST = (array)$data;
            }
        }
    }

    public function __toString()
    {
        return $this->method . " " . $this->path . ($this->querystring != null && strlen($this->querystring) > 0 ? "?" . $this->querystring : "");
    }

}
