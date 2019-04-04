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
 * Classe principal do framework.
 * Manager é um façade para várias classes utilitárias e de serviços do framework.
 *
 * @category    Maestro
 * @package     Core
 * @version     3.0
 * @since       1.0
 * @copyright  Copyright (c) 2003-2017 UFJF (http://www.ufjf.br)
 * @license    http://maestro.org.br
 */

use Composer\Script\Event;

define('MAESTRO_NAME', 'Maestro 3.0');
define('MAESTRO_VERSION', '3.0');
define('MAESTRO_AUTHOR', 'Maestro Team');

/**
 * Constantes para direitos de acesso.
 */
define('A_ACCESS', 1);   // 000001
define('A_QUERY', 1);    // 000001
define('A_INSERT', 2);   // 000010
define('A_DELETE', 4);   // 000100
define('A_UPDATE', 8);   // 001000
define('A_EXECUTE', 15); // 001111
define('A_SYSTEM', 31);  // 011111
define('A_ADMIN', 31);   // 011111
define('A_DEVELOP', 32); // 100000

/**
 * Indices do array em actions.php
 */
define('ACTION_CAPTION', 0);
define('ACTION_PATH', 1);
define('ACTION_ICON', 2);
define('ACTION_TRANSACTION', 3);
define('ACTION_ACCESS', 4);
define('ACTION_ACTIONS', 5);
define('ACTION_GROUP', 6);

/**
 * Constantes para estilos de fetch
 */
define('FETCH_ASSOC', \PDO::FETCH_ASSOC);
define('FETCH_NUM', \PDO::FETCH_NUM);

require_once 'classloader.php';

class Manager
{
    /*
     * Extensão dos arquivos de código.
     */

    static private $fileExtension = '.php';
    /*
     * Caracter separador dos namespaces.
     */
    static private $namespaceSeparator = '\\';
    /*
     * Instância singleton.
     */
    static private $instance = NULL;
    /*
     * Array de configurações conf/conf.php.
     */
    static public $conf = array();
    /*
     * Array de ações conf/actions.php.
     */
    static public $actions = array();
    /*
     * Objeto com dados de login.
     */
    static public $login;
    /*
     * Mensagens definidas.
     */
    static public $msg;
    /*
     * Indica se a chamada é Ajax ou não.
     */
    static public $ajax;
    /*
     * Array com paths registrados para autoload.
     */
    static protected $autoloadPaths = array();
    /*
     * Array com paths dos namespace registrados.
     */
    static protected $namespacePaths = array();
    /*
     * Versão atual
     */
    public $_version;
    /*
     * Autor.
     */
    public $_author;
    /*
     * Path da instalação do Framework.
     */
    public $basePath;
    /*
     * Path da aplicação corrente (sendo executada).
     */
    public $appPath;
    /*
     * Path do arquivo de configuração do Framework (conf.php).
     */
    public $confPath;
    /*
     * Path para armazenamento de arquivo com acesso público.
     */
    public $publicPath;
    /*
     * Path das aplicações instaladas.
     */
    public $appsPath;
    /*
     * Path dos arquivos de classes do Framework.
     */
    public $classPath;
    /*
     * Path do tema em uso.
     */
    public $themePath;
    /*
     * `Path da aplicações Core.
     */
    public $coreAppsPath;
    /*
     * Path do arquivo de configuração em uso (default: conf.php)
     */
    public $configFile;
    /*
     * Array com as classes já carregadas.
     */
    public $autoload = array();
    /*
     * Objeto com dados do contexto de execução.
     */
    public $context;
    /*
     * Objeto do contexto Java (uso com JavaBridge).
     */
    public $javaContext = NULL;
    /*
     * Objeto do contexto do Servlet Java (uso com JavaBridge).
     */
    public $javaServletContext = NULL;
    /*
     * Objeto Cache.
     */
    public $cache;
    /*
     * Nome da Aplicação (ou Módulo) de Admnistração do Framework.
     */
    public $mad;
    /*
     * Array com as classes importadas. 
     */
    public $import = array();
    /*
     * Array com alias para classes (uso com namespaces).
     */
    public $classAlias = array();
    /*
     * Nome da aplicação sendo executada.
     */
    public $app;
    /*
     * Controller sendo executado.
     */
    public $controller;
    /*
     * View sendo executada.
     */
    public $view;
    /*
     * Modo de execução: Produção (PROD) ou Desenvolvimento (DEV)
     */
    public $mode;
    /*
     * Objeto MPage.
     */
    public $page;
    /*
     * Nome do tema em uso.
     */
    public $theme;
    /*
     * URL base para acesso ao Framework.
     */
    public $baseURL;
//    public $application;
    /*
     * Objeto MDatabase que encapsula o acesso a banco de dados.
     */
    public $db = array();
    /*
     * Array com cache dos Controllers já usados.
     */
    public $controllers = array();
    /*
     * DTO (Data Transfer Object): objeto com dados da requisição,
     * transversal a todas as camadas.
     */
    public $data;
    /*
     * Objeto MLog.
     */
    public $log;
    /*
     * Objeto MTrace.
     */
    public $trace;

    /**
     * Construtor.
     * Construtor da classe Manager.
     */
    public function __construct()
    {

    }

    /**
     * Cria (se não existe) e retorna a instância singleton da class Manager.
     *
     * @returns (object) Instance of Manager class
     *
     */
    public static function getInstance()
    {
        if (self::$instance == NULL) {
            self::$instance = new Manager();
        }
        return self::$instance;
    }

    /**
     * Inicialização do Framework.
     * Método chamado pelo FrontPage (index.php) para inicializar os atributos
     * da classe Manager e registrar os Autoloaders.
     * @param string $configFile
     * @param string $basePath
     * @param string $app
     */
    public static function init($configFile = '', $basePath = '', $app = '')
    {
        $m = Manager::getInstance();
        $m->basePath = $basePath;
        $m->appsPath = $basePath . '/apps';
        $m->coreAppsPath = $basePath . '/core/apps';
        $m->app = $app;
        $m->confPath = $basePath . '/core/conf';
        $m->publicPath = $basePath . '/public';
        $m->classPath = $basePath . '/core/classes';
        $m->loadAutoload($basePath . '/vendor/autoload_manager.php');
        $managerConfigFile = $m->confPath . '/conf.php';
        $m->loadConf($managerConfigFile);
        if ($configFile != $managerConfigFile) { // carrega configurações adicionais
            $m->loadConf($managerConfigFile);
        }
        register_shutdown_function("shutdown");
    }

    /**
     * Instancia (caso não exista) e retorna um objeto utilitário ou de serviço
     * com base no nome da classe.
     * @param string $class
     * @param string $param
     * @return object
     */
    private function getObject($class, $param = NULL)
    {
        if (is_null(self::$instance->$class)) {
            $className = 'M' . $class;
            self::$instance->$class = new $className($param);
        }
        return self::$instance->$class;
    }

    public function __get($name)
    {
        return isset(Manager::$$name) ? Manager::$$name : null;
    }

    public function __set($name, $value)
    {
        if (property_exists('Manager', $name)) {
            Manager::$$name = $value;
        } else {
            $this->$name = $value;
        }
    }

    /**
     * Retorna o path base do Framework.
     * @return string
     */
    public static function getHome()
    {
        return self::$instance->basePath;
    }

    /**
     * Retorna o path absoluto de $relative.
     * @return string
     */
    public static function getAbsolutePath($relative = NULL)
    {
        $path = self::$instance->getHome();
        if ($relative) {
            $path .= '/' . $relative;
        }
        return $path;
    }

    /**
     * Retorna o path da classe $class.
     * @return string
     */
    public static function getClassPath($class = '')
    {
        $path = self::$instance->classPath;
        if ($class) {
            $path .= '/' . $class;
        }
        return $path;
    }

    /**
     * Retorna o path do arquivo de configuração $conf.
     * @return string
     */
    public static function getConfPath($conf = '')
    {
        $path = self::$instance->confPath;
        if ($conf) {
            $path .= '/' . $conf;
        }
        return $path;
    }

    /**
     * Retorna o path de um arquivo público.
     * @return string
     */
    public static function getPublicPath($app = '', $module = '', $file = '')
    {
        if ($app) {
            $path = self::$instance->appsPath . '/' . $app;
            if ($module) {
                $path .= '/modules/' . $module;
            }
            $path .= '/public';
        } else {
            $path = self::$instance->publicPath;
        }
        if ($file) {
            $path .= '/' . $file;
        }
        return $path;
    }

    /**
     * Retorna o path do tema $theme.
     * @return string
     */
    public static function getThemePath($theme = '')
    {
        $path = self::$instance->themePath;
        if ($path == '') {
            $path = self::getPublicPath(self::getApp(), '', 'themes/' . self::getTheme());
            if (!is_dir($path)) {
                $path = self::getPublicPath('', '', 'themes/' . self::getTheme());
            }
            self::$instance->themePath = $path;
        }
        if ($theme) {
            $path .= '/' . $theme;
        }
        return $path;
    }

    /**
     * Retorna o path do módulo/arquivo ($module/$file) na aplicação em execução.
     * @return string
     */
    public static function getAppPath($file = '', $module = '', $app = '')
    {
        if ($app) {
            $path = self::$instance->appsPath . '/' . $app;
        } else {
            $path = self::$instance->appPath;
        }
        if ($module) {
            $path .= '/modules/' . $module;
        }
        if ($file) {
            $path .= '/' . $file;
        }

        return $path;
    }

    /**
     * Retorna o path do módulo/arquivo ($module/$file) na aplicação em execução.
     * @return string
     */
    public static function getModulePath($module, $file)
    {
        return self::$instance->getAppPath($file, $module);
    }

    /**
     * Retorna o path Core do Framework.
     * @return string
     */
    public static function getFrameworkPath($file = '')
    {
        $path = self::$instance->getHome() . '/core';
        if ($file) {
            $path .= '/' . $file;
        }
        return $path;
    }

    /**
     * Retorna o path do arquivo $file na área Var. $session indica se arquivo
     * será acessível apenas durante a sessão em que foi criado.
     * @param string $file
     * @param boolean $session
     * @return string
     */
    public static function getFilesPath($file = '', $session = false)
    {
        $path = self::$instance->getHome() . '/core/var/files';
        if ($file) {
            if ($session) {
                $sid = self::$instance->getSession()->getId();
                $info = pathinfo($file);
                $file = md5(basename($file) . $sid) . ($info['extension'] ? '.' . $info['extension'] : '');
            }
            $path .= '/' . $file;
        }
        return $path;
    }

    /**
     * Registra uma nova classe de Autoloader.
     * O novo Autoloader será colocado antes do "manager::autoload" e depois dos outros autoloaders já existentes.
     *
     * @param string $namespace Namespace usado pela classe.
     * @param string $includePath Path das classes que será carregadas.
     * @param boolean $append
     */
    public static function registerAutoloader($namespace, $includePath, $append = false)
    {
        $classLoader = new ClassLoader($namespace, $includePath);
        self::$namespacePaths[$namespace] = $includePath;
        $array = array($classLoader, 'loadClass');
        if ($append) {
            self::$enableIncludePath = false;
            spl_autoload_register($array);
        } else {
            spl_autoload_unregister(array('Manager', 'autoload'));
            spl_autoload_register($array);
            spl_autoload_register(array('Manager', 'autoload'));
        }
    }

    /**
     * Adiciona path para classes que são carregadas via Autoloader.
     * @param string $includePath
     */
    public static function addAutoloadPath($includePath)
    {
        $path = realpath($includePath);
        if ($path) {
            self::$autoloadPaths[] = $path;
        }
    }

    /**
     * Adiciona classe a ser carregada via Autoloader.
     * @param type $className Nome da classe.
     * @param type $classPath Path da classe.
     */
    public static function addAutoloadClass($className, $classPath)
    {
        if (file_exists($classPath)) {
            self::$instance->autoload[$className] = $classPath;
        }
    }

    /**
     * Carrega uma classe.
     * @param string $className Nome da classe.
     * @return type
     */
    public static function autoload($className)
    {
        $class = strtolower($className);
        $file = self::$instance->autoload[$class];
        if ($file != '') {
            if (file_exists($file)) {
                include_once($file);
            } else {
                $file = self::$instance->getClassPath($file);
                if (file_exists($file)) {
                    include_once($file);
                }
            }
        } else {
            foreach (self::$autoloadPaths as $path) {
                $file = $path . DIRECTORY_SEPARATOR . $className . '.php';
                if (is_file($file)) {
                    include_once($file);
                    return;
                } else {
                    $file = $path . DIRECTORY_SEPARATOR . $class . '.php';
                    if (is_file($file)) {
                        include_once($file);
                        return;
                    }
                }
            }
        }
    }

    /**
     * Configura o framework para execução offline
     */
    public static function setupContext($context = '', $data = NULL)
    {
        self::$instance->initialize();
        self::$instance->controller->setupContext($context, $data);
    }

    /**
     * Processa a requisição feita via browser. Executado pelo FrontPage (index.php).
     */
    public static function processRequest($return = false)
    {
        self::$instance->initialize();
        return self::$instance->handler($return);
    }

    /**
     * Processa a requisição feita via browser após a inicialização do Framework,
     * delegando a execução para o FrontController.
     */
    public static function handler($return = false)
    {
        self::$instance->controller->handlerRequest();
        return self::$instance->controller->handlerResponse($return);
    }

    /**
     * Inicialização básica do Framework.
     * Inicializa "variáveis globais", mensagens do framework, log e FrontController.
     */
    public static function initialize()
    {
        if (self::$instance->java = ($_SERVER["SERVER_SOFTWARE"] == "JavaBridge")) {
            require_once(self::$instance->home . "/java/Java.inc");
            self::$instance->javaContext = java_context();
            self::$instance->javaServletContext = java_context()->getServletContext();
        }
        self::$instance->getObject('login');
        self::$msg = new MMessages(self::$instance->getOptions('language'));
        self::$msg->loadMessages();
        self::$instance->mode = self::$instance->getOptions("mode");
        date_default_timezone_set(self::$instance->getOptions("timezone"));
        setlocale(LC_ALL, self::$instance->getOptions("locale"));
        self::$instance->setLog('manager');
        self::$instance->mad = self::$instance->conf['mad']['module'];
        self::$instance->controller = MFrontController::getInstance();
        $varPath = self::$instance->getOptions('varPath') . '/maestro3';
        if (!file_exists($varPath)) {
            mkdir($varPath);
            mkdir($varPath . '/templates');
            mkdir($varPath . '/files');
            mkdir($varPath . '/log');
        }
    }

    /**
     * Carrega configurações a partir de um arquivo conf.php.
     * @param string $configFile
     */
    public function loadConf($configFile)
    {
        $conf = require($configFile);
        self::$conf = MUtil::arrayMergeOverwrite(self::$conf, $conf);
    }

    /**
     * Carrega ações a partir de um arquivo actions.php.
     * @param string $actionsFile
     */
    public function loadActions($actionsFile)
    {
        if (file_exists($actionsFile)) {
            $actions = require($actionsFile);
            self::$actions = MUtil::arrayMergeOverwrite(self::$actions, $actions);
        }
    }

    /**
     * Carrega definições para Autoload de classes.
     * @param string $autoloadFile
     */
    public function loadAutoload($autoloadFile)
    {
        $autoload = require($autoloadFile);
        self::$instance->autoload = array_merge(self::$instance->autoload, $autoload);
    }

    /**
     * Retorna o nome a aplicação em execução.
     */
    public static function getApp()
    {
        return self::getContext()->getApp();
    }

    /**
     * Retorna o nome do módulo em execução.
     */
    public static function getModule()
    {
        return self::getContext()->getModule();
    }

    /**
     * Retorna o nome do controller em execução.
     */
    public static function getCurrentController()
    {
        return self::$instance->controller->getController();
    }

    /**
     * Retorna o nome da action em execução.
     */
    public static function getCurrentAction()
    {
        return self::$instance->controller->getAction();
    }

    /**
     * Retorna o objeto DTO (variável $data)
     */
    public static function getData($attribute = NULL)
    {
        $data = self::$instance->data;
        if ($attribute != NULL) {
            $data = $data->$attribute;
        }
        return $data;
    }

    /**
     * Atualiza o objeto DTO (variável $data)
     */
    public static function setData($value)
    {
        self::$instance->data = $value;
    }

    /**
     * Retorna o objeto MRequest (instanciado no FrontController).
     */
    public static function getRequest()
    {
        return self::$instance->controller->getRequest();
    }

    /**
     * Retorna o nome da aplicação/módulo de administração do Framework.
     * @return string
     */
    public static function getMAD()
    {
        return self::$instance->mad;
    }

    /**
     * Retorna o objeto FrontController.
     * @return type
     */
    public static function getFrontController()
    {
        return self::$instance->controller;
    }

    /**
     * Retorna informação sobre o tipo da requisição.
     *
     * @return (bool) True se for uma chamada Ajax.
     */
    public static function isAjaxCall()
    {
        return !is_null(self::getRequest()) && self::getRequest()->isAjax();
    }

    /**
     * Returns information about the king of the Ajax request.
     *
     * @return (bool) True if is is an Ajax event call, otherwise False.
     */
    public static function isAjaxEvent()
    {
        return self::$instance->getRequest()->isAjaxEvent();
    }

    /**
     * Returns information about the type of request.
     *
     * @return (bool) True if it is a dynamic file download.
     */
    public static function isDownload()
    {
        return self::$instance->getRequest()->isDownload();
    }

    public static function SPI()
    {
        return MUtil::getBooleanValue(self::$instance->getOptions('SPI'));
    }

    /*
     * Retorna 1 se o servidor está em modo de desenvolvimento
     */
    public static function DEV()
    {
        return (self::$instance->getOptions('mode') == 'DEV');
    }

    /*
     * Retorna 1 se o servidor está em modo de producao
     */
    public static function PROD()
    {
        return (self::$instance->getOptions('mode') == 'PROD');
    }

    public static function HOMOLOG()
    {
        return (self::$instance->getOptions('homolog') === true);
    }

    public static function AUDIT() {
        $audit = self::getConf('audit');
        return isset($audit) && $audit['enabled'] === true;
    }

    public static function getAuditors() {
        return self::AUDIT() ? self::getConf('audit')['auditors'] : [];
    }

    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        $codes = self::$instance->getConf('logs.errorCodes');

        if (self::supressWarning($errno, $errstr)) {
            return;
        }

        if (in_array($errno, $codes)) {
            self::logMessage("[ERROR] [Code] $errno [Error] $errstr [File] $errfile [Line] $errline");
        }
    }

    /*
     * Essa função serve para evitar a inundação de warnings que ocorre no PHP7 devido
     * ao fim dos erros E_STRICT.
     * Ver: http://stackoverflow.com/questions/36079651/silence-declaration-should-be-compatible-warnings-in-php-7
     */
    private static function supressWarning($errno, $errstr)
    {
        return PHP_MAJOR_VERSION >= 7
            && $errno == 2
            && strpos($errstr, 'Declaration of') === 0;
    }

    public function getPathOfAlias($alias)
    {
        $path = $alias;
        if ($alias == 'application') {
            $path = 'apps/' . self::$instance->getApp();
        }
        if ($alias == 'module') {
            $path = 'modules/' . self::$instance->getModule();
        }
        return $path;
    }

    public static function getNamespacePath($namespace)
    {
        $path = self::getHome();
        $tokens = explode('::', $namespace);
        foreach ($tokens as $token) {
            $path .= '/' . self::$instance->getPathOfAlias($token);
        }
        return $path;
    }

    public static function getNamespaceAlias($namespace)
    {
        $path = str_replace(self::$namespaceSeparator, DIRECTORY_SEPARATOR, $namespace);
        $tokens = explode(DIRECTORY_SEPARATOR, $path);
        if (is_array($tokens)) {
            $last = array_pop($tokens);
            if ($last == '*') {
                $path = str_replace('*', '', $path);
                $path = self::$namespacePaths[$tokens[0]] . DIRECTORY_SEPARATOR . $path;
                $files = self::$instance->listFiles($path, 'f');
                if (is_array($files)) {
                    foreach ($files as $file) {
                        $pathinfo = pathinfo($file);
                        $alias = str_replace('.' . $pathinfo['extension'], '', $pathinfo['filename']);
                        $original = str_replace('*', '', $namespace) . $alias;

                        if (!array_key_exists($original, self::$instance->classAlias)) {
                            class_alias($original, $alias);
                            self::$instance->classAlias[$original] = $alias;
                        }
                    }
                }
            } else {
                if (!self::$instance->classAlias[$namespace]) {
                    class_alias($namespace, $last);
                    self::$instance->classAlias[$namespace] = $last;
                }
            }
        }
    }

    public static function existsNS($namespace)
    {
        $file = self::$instance->getNamespacePath($namespace);
        return file_exists($file);
    }

    public static function import($namespace, $class = '', $extension = '.php')
    {
        $result = null;
        if (self::$instance->import[$namespace]) {
            $result = self::$instance->import[$namespace];
        } elseif (($class != '') && ($path = self::$instance->autoload[$class])) {
            $result = $path;
        } else {
            if (strpos($namespace, '\\') === false) { // $namespace representa um path
                $path = self::$instance->getNamespacePath($namespace);
                $pathinfo = pathinfo($path);
                if ($pathinfo['filename'] == '*') {
                    $files = self::$instance->listFiles($pathinfo['dirname'], 'f');
                    if (count($files)) {
                        foreach ($files as $file) {
                            $class = str_replace($extension, '', $file);
                            $ns = str_replace('*', $class, $namespace);
                            $path = $pathinfo['dirname'] . '/' . $file;
                            self::$instance->autoload[strtolower($class)] = $path;
                            self::$instance->import[$ns] = $class;
                        }
                    }
                } else {
                    $path .= ($pathinfo['extension'] == '' ? $extension : '');
                    if ($result = file_exists($path)) {
                        $class = ($class != '') ? $class : $pathinfo['basename'];
                        self::$instance->autoload[strtolower($class)] = $path;
                        self::$instance->import[$namespace] = $class;
                        $result = $path;
                    } else {
                        $errmsg = _M('File not found: ') . $path;
                        self::$instance->logMessage($errmsg);
                    }
                }
            } else { // $namespace representa um namespace
                self::$instance->getNamespaceAlias($namespace);
            }
        }
        return $result;
    }

    public static function getConf($key)
    {
        $k = explode('.', $key);
        $conf = self::$conf;
        foreach ($k as $token) {
            if (!is_array($conf)) {
                return null;
            }
            if (!array_key_exists($token, $conf)) {
                return null;
            }
            $conf = $conf[$token];
        }

        return $conf;
    }

    public static function getActions($action = '')
    {
        if ($action != '') {
            $actions = self::getAction($action);
            return $actions[ACTION_ACTIONS];
        } else {
            return self::$actions;
        }
    }

    public static function getAction($action)
    {
        $actions = self::$actions;
        $k = explode('.', $action);
        $actions = $actions[$k[0]];
        for ($i = 1; $i < count($k); $i++) {
            $actions = $actions[ACTION_ACTIONS][$k[$i]];
        }
        return $actions;
    }

    public static function getOptions($key)
    {
        return isset(self::$conf['options'][$key]) ? self::$conf['options'][$key] : '';
    }

    public static function getParams($key)
    {
        return isset(self::$conf['params'][$key]) ? self::$conf['params'][$key] : '';
    }

    public static function setConf($key, $value)
    {
        $k = explode('.', $key);
        $n = count($k);
        if ($n == 1) {
            self::$conf[$k[0]] = $value;
        } else if ($n == 2) {
            self::$conf[$k[0]][$k[1]] = $value;
        } else if ($n == 3) {
            self::$conf[$k[0]][$k[1]][$k[2]] = $value;
        } else if ($n == 4) {
            self::$conf[$k[0]][$k[1]][$k[2]][$k[3]] = $value;
        }
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $cond (tipo) desc
     * @param $msg ' (tipo) desc
     * @param $goto ='' (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public static function assert($cond, $msg = '', $goto = '')
    {
        if ($cond == false) {
            self::$instance->logMessage('[ERROR]' . $msg);
            self::$instance->error($msg, $goto, _M('Fatal error'));
        }
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $url (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public static function setDispatcher($url)
    {
        self::$instance->dispatch = $url;
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns (tipo) desc
     *
     */
    public static function getContext()
    {
        return self::$instance->controller->getContext();
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns MSession desc
     *
     */
    public static function getSession()
    {
        return self::$instance->session;
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns MSession desc
     *
     */
    public static function setSession($session)
    {
        self::$instance->session = $session;
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns (tipo) desc
     *
     */
    public static function getAuth()
    {
        if (is_null(self::$instance->auth)) {
            $class = strtolower(self::$conf['login']['class']);
            if ($class == NULL) {
                $class = "mauthdb";
            }
            if (!(self::$instance->import('security::' . $class, $class))) {
                self::$instance->import('modules::' . self::$conf['login']['module'] . '::classes::' . $class, $class, self::$instance->php);
            }
            self::$instance->auth = new $class();
        }
        return self::$instance->auth;
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns (tipo) desc
     *
     */
    public static function getPerms()
    {
        if (is_null(self::$instance->perms)) {
            $class = strtolower(self::$conf['login']['perms']);

            if ($class) {
                if (!(self::$instance->import('security::' . $class, $class))) {
                    self::$instance->import('modules::' . self::$conf['login']['module'] . '::classes::' . $class, $class, self::$instance->php);
                }
                return self::$instance->perms = new $class();
            }
        }

        return self::$instance->getObject('perms');
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns \MLogin
     *
     */
    public static function getLogin()
    {
        return self::$instance->getAuth()->getLogin();
    }

    /**
     * Returns the logged user: an instance of class configured within mad.module and mad.user
     */
    public static function getLoggedUser()
    {
        return self::getLogin()->getUser();
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns MPage
     *
     */
    public static function getPage()
    {
        return self::$instance->getObject('page');
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns (tipo) desc
     *
     */
    public static function getAjax()
    {
        return self::$ajax;
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns (tipo) desc
     *
     */
    public static function getCache()
    {
        if (!self::$instance->cache) {
            if (isset(self::$instance->conf['cache']['type'])) {
                self::$instance->cache = new MCache(self::$instance->conf['cache']['type']);
            } else
                return false;
        }
        return self::$instance->cache;
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns (tipo) desc
     *
     */
    public static function getResult()
    {
        return self::$instance->controller->result;
    }

    public static function getMessage($key, $parameters = array())
    {
        return self::$msg->get($key, $parameters);
    }

    public static function getBaseURL($absolute = false)
    {
        return $absolute ? self::$instance->getRequest()->getBaseURL(true) : self::$instance->baseURL;
    }

    public static function getAppURL($app = '', $file = '', $absolute = false)
    {
        $app = ($app ?: self::$instance->getApp());
//        return $appURL = self::$instance->getBaseURL($absolute) . (self::$instance->java ? '' : '/index.php') . '/' . $app . ($file ? '/' . $file : '');
        return $appURL = self::$instance->getBaseURL($absolute) . '/index.php' . '/' . $app . ($file ? '/' . $file : '');
    }

    public static function getStaticURL($app = '', $file = '', $absolute = false)
    {
        $app = ($app ?: self::$instance->getApp());
//        return self::$instance->getBaseURL($absolute) . (self::$instance->java ? '' : '/apps') . '/' . $app . '/public' . ($file ? '/' . $file : '');
        return self::$instance->getBaseURL($absolute) . '/apps' . '/' . $app . '/public' . ($file ? '/' . $file : '');
    }

    public static function getDownloadURL($controller = '', $file = '', $inline = false, $absolute = true)
    {
        return self::$instance->getAppURL('core/download', $controller . '/' . ($inline ? 'inline' : 'save') . '/' . $file, $absolute);
    }

    public static function getURL($action = 'main/main', $args = array())
    {
        if (strtoupper(substr($action, 0, 4)) == 'HTTP') {
            return $action;
        }
        if (Manager::getOptions('compatibility')) {
            $action = str_replace(':', '/', $action);
        }
        $url = self::$instance->getContext()->buildURL($action, $args);
        return $url;
    }

    /**
     * Gets absolute virtual path of $rel (relative filename) from browser's address
     */

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $rel (tipo) desc
     * @param $module (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public static function getAbsoluteURL($rel, $module = NULL)
    {
        $url = self::$instance->getBaseURL(true);
        if ($module) {
            $url .= '/modules/' . $module;
        }
        // prepend path separator if necessary
        if (substr($rel, 0, 1) != '/') {
            $url .= '/';
        }
        $url .= $rel;
        return $url;
    }

    /**
     * Gets absolute virtual path of $rel for selected theme
     */

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $rel (tipo) desc
     * @param $name (tipo) desc
     * @param $default =NULL (tipo) desc
     * @param $module =NULL (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public static function getThemeURL($file = '')
    {
        if (substr($file, 0, 1) == '/') {
            return $file;
        }
        $theme = self::$instance->getTheme();
        $app = self::$instance->getApp();
        $path = self::getPublicPath($app, '', 'themes/' . $theme);
        if (is_dir($path)) {
            $url = self::$instance->getAbsoluteURL("apps/{$app}" . Manager::getOptions('srcPath') . "/public/themes/{$theme}/{$file}");
        } else {
            $url = self::$instance->getAbsoluteURL("public/themes/{$theme}/{$file}");
        }
        return $url;
    }

    /**
     * Return current URL.
     * Returns the URL address of the current page.
     *
     * @returns (string) URL address
     *
     */
    public static function getCurrentURL($parametrized = false)
    { //static
        if (!($url = self::$instance->getRequest()->getURL())) {
            //$url = self::$instance->baseURL . (self::$instance->java ? '' : '/' . self::$instance->getConf('options.dispatch'));
            $url = self::$instance->baseURL . '/' . self::$instance->getConf('options.dispatch');
        }
        if ($parametrized) {
            $url .= "?";
            foreach (Manager::getData() as $key => $value) {
                if ((strpos($key, "__") !== 0) && (strpos($key, "grid") !== 0)) {
                    $value = urlencode($value);
                    $url .= $key . "=" . $value . "&";
                }
            }
        }
        return $url;
    }

    /**
     * @todo TRANSLATION
     * Retorna
     * O metodo _REQUEST provÃª uma forma simples e rÃ¡pida para se ter acesso Ã s
     * variÃ¡veis, alÃ©m de garantir a compatibilidade com versÃµes futuras do PHP.
     * Utilizando comandos PHP, seria necessÃ¡rio utilizar $_REQUEST, $_GET, $_POST
     * ou global ao passo este mÃ©todo possibilita, alÃ©m da busca num, a busca em
     * todas as informacoes.
     * Caso vocÃª queira obter apenas o valor da variÃ¡veis provenientes de
     * uma dessas opcoes, por exemplo GET, passe essa palavra como segundo
     * parÃ¢metro.
     *
     * @param (mixed) $vars String ou array: variÃ¡veis das quais se deseja obter o valor
     * @param (string) $from De onde obter os dados. Pode ser 'GET', 'POST',
     *                       'SESSION', 'REQUEST' alÃ©m do padrÃ£o 'ALL' que
     *                       retorna todos os dados.
     * @param (string) $order Onde pesquisar primeiro POST ou GET. Por padrÃ£o a
     *                        pesquisa Ã© feita de acordo com a configuraÃ§Ã£o do php.ini .
     *                        Para forÃ§ar a ordem, informe "PG" ou "GP" (P=post, G=get)
     *
     * @return (array) Os valores das variÃ¡veis solicitadas
     */
    public static function _REQUEST($vars, $from = 'ALL', $order = '')
    {
        if (is_array($vars)) {
            foreach ($vars as $v) {
                $values[$v] = self::_REQUEST($v, $from);
            }

            return $values;
        } else {
            // Seek in all scope?
            if ($from == 'ALL') {
                // search in REQUEST
                if (!isset($value)) {
                    $value = $_REQUEST["$vars"];
                }

                // Not found in REQUEST? try GET or POST
                // Order? Default is use the same order as defined in php.ini ("EGPCS")
                if (!isset($order)) {
                    $order = ini_get('variables_order');
                }

                if (!isset($value)) {
                    if (strpos($order, 'G') < strpos($order, 'P')) {
                        $value = $_GET["$vars"];

                        // If not found, search in post
                        if (!isset($value)) {
                            $value = $_POST["$vars"];
                        }
                    } else {
                        $value = $_POST["$vars"];

                        // If not found, search in get
                        if (!isset($value)) {
                            $value = $_GET["$vars"];
                        }
                    }
                }

                // If we still didn't have the value
                // let's try in the global scope
                if ((!isset($value)) && ((strpos($vars, '[')) === false)) {
                    $value = $_GLOBALS["$vars"];
                }

                // If we still didn't has the value
                // let's try in the session scope

                if (!isset($value)) {
                    $value = $_SESSION["$vars"];
                }
            } else if ($from == 'GET') {
                $value = $_GET["$vars"];
            } elseif ($from == 'POST') {
                $value = $_POST["$vars"];
            } elseif ($from == 'SESSION') {
                $value = $_SESSION["$vars"];
            } elseif ($from == 'REQUEST') {
                $value = $_REQUEST["$vars"];
            }

            return $value;
        }
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns (tipo) desc
     *
     */
    public static function getSysTime($format = 'd/m/Y H:i:s')
    {
        return date($format);
    }

    public static function getSysDate($format = 'd/m/Y')
    {
        return date($format);
    }

    public static function date($value, $format = '')
    {
        return new MDate($value, $format);
    }

    public static function timestamp($value, $format = '')
    {
        return new MTimestamp($value, $format);
    }

    public static function currency($value)
    {
        return new MCurrency($value);
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns (tipo) desc
     *
     */
    public static function checkLogin($deny = true)
    {
        $login = self::$instance->getAuth()->checkLogin();
        if (!$login && $deny) {
            self::storeURI();
            throw new ELoginException(_M('Login required!'));
        }
        return $login;
    }

    /**
     * Guarda a URI acessada na sessao.
     */
    private static function storeURI()
    {
        $request = self::getRequest();
        if ($request->getRequestType() == 'GET') {
            $session = self::getSession()->container('maestro');
            $session->setExpirationSeconds(3 * 60);
            $session->originalURL = $request->getURI();
        }
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $trans (tipo) desc
     * @param $access (tipo) desc
     * @param $deny (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public static function checkAccess($trans, $access, $deny = false)
    {
        return self::$instance->getObject('perms')->checkAccess($trans, $access, $deny);
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns (tipo) desc
     *
     */
    public static function isHostAllowed()
    {
        $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
        $returnValue = false;

        foreach (self::$instance->getOptions('hosts.allow') as $h) {
            if ($REMOTE_ADDR == $h) {
                $returnValue = true;
            }

            // Is it a interval of IP's?
            if ((strpos($h, '-') > 0) && (substr($h, 0, strrpos($h, '.')) == substr($REMOTE_ADDR, 0, strrpos($REMOTE_ADDR, '.')))) {
                list($firstIP, $lastIP) = explode('-', $h);
                $lastIP = substr($firstIP, 0, strrpos($firstIP, '.') + 1) . $lastIP;

                $remoteIP = substr($REMOTE_ADDR, strrpos($REMOTE_ADDR, '.') + 1, strlen($REMOTE_ADDR));
                $startIP = substr($firstIP, strrpos($firstIP, '.') + 1, strlen($firstIP));
                $endIP = substr($lastIP, strrpos($lastIP, '.') + 1, strlen($lastIP));

                if (($startIP < $remoteIP) && ($endIP > $remoteIP)) {
                    $returnValue = true;
                }
            }
        }

        foreach (self::$instance->getOptions('hosts.allow') as $h) {
            if ($REMOTE_ADDR == $h) {
                $returnValue = false;
            }
        }

        return $returnValue;
    }

    //
    // Factories Methods
    //     GetDatabase
    //     GetBusiness
    //     GetUI
    //     GetTheme
    //
    public static function getDatabase($conf = NULL)
    {
        $conf = $conf ?: 'maestro';
        if (isset(self::getInstance()->db[$conf])) {
            $db = self::getInstance()->db[$conf];
        } else {
            try {
                $db = new database\MDatabase($conf);
                self::setDatabase($db, $conf);
            } catch (Exception $e) {
                throw $e;
            }
        }
        return $db;
    }

    public static function setDatabase(\IDataBase $db, $conf)
    {
        self::getInstance()->db[$conf] = $db;
    }

    public static function getBusiness($module, $name = 'main', $data = NULL)
    {
        $class = 'Business' .
            strtoupper(substr($module, 0, 1)) . substr($module, 1) .
            strtoupper(substr($name, 0, 1)) . substr($name, 1);

        $filename = self::getAppPath() . '/modules/' . $module . '/models/' . $name . '.class.php';
        if (!file_exists($filename)) {
            throw new EModelException(_M('Error in getBusiness: Class not Found! <BR>Class name: ') . $class . '<BR/><BR/>This class should exist in file ' . self::$instance->getConf('home.modules') . '/' . $module . "/model/$name.class.php" . self::$instance->php);
        }
        $app = self::getApp();
        self::$instance->import('apps::' . $app . '::modules::' . $module . '::models::' . $name . '.class.php', $class);
        // instanciate a new class
        $business = new $class();
        $business->_bmodule = $module;
        $business->_bclass = $name;
        $business->onCreate($data);
        return $business;
    }

    public function getModel($module, $name = 'main', $data = NULL)
    {
        $filename = self::getAppPath() . '/modules/' . $module . '/models/' . $name . '.php';
        if (file_exists($filename)) {
            $model = $module . '\\models\\' . $name;
            if (class_exists($model)) {
                return new $model($data);
            } else {
                $namespace = self::$conf['mad']['namespace'];
                $model = $namespace . '\\models\\' . $name;
                return new $model($data);
            }
        } else {
            return self::getBusiness($module, $name, $data);
        }
    }

    public static function getModelMAD($name = 'main', $data = NULL)
    {
        $module = self::$conf['mad']['module'];
        $name = self::$conf['mad'][$name];
        $configFile = self::$instance->getModulePath($module, '/conf/conf.php');
        self::$instance->loadConf($configFile);
        self::registerAutoloader($module, self::getAppPath() . '/modules/');
        return self::getModel($module, $name, $data);
    }

    public static function getUI()
    {
        return self::$instance->getObject('ui');
    }

    public static function getTheme()
    {
        return self::$conf['theme']['name'];
    }

    public static function setTheme($theme)
    {
        self::$conf['theme']['name'] = $theme;
        $path = self::getPublicPath(self::$instance->getApp(), '', 'themes/' . $theme);
        if (!is_dir($path)) {
            $path = self::getPublicPath('', '', 'themes/' . $theme);
        }
        self::$instance->themePath = $path;
    }


    public static function getLocale()
    {
        return self::$conf['options']['locale'][0];
    }

    // get the class name of painter
    public static function getPainter()
    {
        if (is_null(self::$instance->painter)) {
            self::$instance->painter = "M" . self::$instance->getOptions('painter') . "Painter";
        }
        $painter = self::$instance->painter ?: 'MHtmlPainter';
        return new $painter;
    }

    public static function getController($app, $module, $controller, $context = null)
    {
        $class = "{$controller}Controller";
        if (self::$instance->controllers[$class]) {
            self::$instance->logMessage("[getController  from cache]");
            return self::$instance->controllers[$class];
        }
        if (self::$instance->controller->getContainer()) {
            $namespace = ($module ?: $app) . "\\controllers\\" . $class;
            self::$instance->logMessage("[Manager::getController  {$namespace}");
            $c = self::$instance->controller->container->get($namespace);
        } else {
            if ($fileMap = self::$instance->getContext()->getFileMap()) {
                $namespace = ($module ? $module . '\\' : '') . "controllers\\{$controller}controller";
                mdump($namespace);
                require_once($fileMap[$namespace]);
                $c = new $class;
            } else {
                $namespace = $app . '\\' . ($module ? $module . '\\' : '') . "controllers\\{$controller}controller";
                mdump($namespace);
                if (class_exists($namespace)) {
                    self::$instance->logMessage("[Manager::getController  {$namespace}");
                    $c = new $namespace();
                } else {
                    $ctx = $context ?: self::getContext();
                    $namespace = $ctx->getNameSpace($app, $module, $class);
                    self::$instance->logMessage("[Manager::getController  {$namespace}");
                    self::$instance->import($namespace);
                    $c = new $class();
                }
            }
        }
        $c->setApplication($app);
        $c->setModule($module);
        $c->setName($controller);
        //$c->init();
        self::$instance->controllers[$class] = $c;
        return $c;
    }

    public static function serviceExists($app, $module, $service)
    {
        $class = "{$service}Service";
        $namespace = self::getContext()->getNameSpace($app, $module, $class, 'services');
        return self::$instance->import($namespace) !== false;
    }

    public static function getServiceByName($service, $module = null, $app = null)
    {
        $module = ($module) ?: static::getModule();
        $app = ($app) ?: static::getApp();
        return static::getService($app, $module, $service);
    }

    public static function getAppService($service)
    {
        $module = '';
        $app = static::getApp();
        return static::getService($app, $module, $service);
    }

    public static function getService($app, $module, $service)
    {
        $class = "{$service}Service";
        if (self::$instance->controller->container) {
            $namespace = ($module ?: $app) . "\\services\\" . $class;
            mtrace('namespace for container = ' . $namespace);
            $s = self::$instance->controller->container->get($namespace);

        } else {
            if ($fileMap = self::$instance->getContext()->getFileMap()) {
                $lowerClass = strtolower($class);
                $namespace = ($module ? $module . '\\' : '') . "services\\{$lowerClass}";
                mdump($namespace);
                require_once($fileMap[$namespace]);
                $s = new $class;
            } else {
                $namespace = self::getContext()->getNameSpace($app, $module, $class, 'services');
                mtrace('namespace = ' . $namespace);
                self::$instance->import($namespace);
                self::$instance->logMessage("[Manager::getService  {$namespace}");
                if (self::$instance->controllers[$namespace]) {
                    self::$instance->logMessage("[getService from cache]");
                    return self::$instance->controllers[$namespace];
                }
                $s = new $class();
            }
        }
        //$s = new $class();
        $s->setApplication($app);
        $s->setModule($module);
        $s->setName($service);
        self::$instance->controllers[$class] = $s;
        return $s;
    }

    public static function getAPIService($app, $module, $api, $system, $service)
    {
        $class = "$api/{$service}Service";
        //$namespace = self::getContext()->getNameSpace($app, $module, $service, $api);
        //mtrace('namespace = ' . $namespace);
        //self::$instance->import($namespace);
        //self::$instance->logMessage("[Manager::getAPIService  {$namespace}");
        if (self::$instance->controller->container) {
            if ($system != '') {
                $namespace = ($module ?: $app) . "\\{$api}\\{$system}\\{$service}Service";
            } else {
                $namespace = ($module ?: $app) . "\\{$api}\\{$service}Service";
            }
            mtrace('namespace for container = ' . $namespace);
            $s = self::$instance->controller->container->get($namespace);
        } else {
            //    $s = new $class();
        }
        $s->setApplication($app);
        $s->setModule($module);
        $s->setName($service);
        self::$instance->controllers[$class] = $s;
        return $s;
    }

    public static function getControllers()
    {
        return self::$instance->controllers;
    }

    public static function getView($app = '', $module = '', $controller = '', $view = '')
    {
        if (class_exists('mview', true)) {
            self::$instance->view = new MView($app, $module, $controller, $view);
        } else {
            self::$instance->view = new MBaseView($app, $module, $controller, $view);
        }
        self::$instance->view->init();
        return self::$instance->view;
    }

    //

    //

    /**
     * Dialogs and Error Handling
     * Dialogs and Error Handling
     *     Error
     *     Information
     *     Confirmation
     *     Question
     *     Prompt
     *
     * @param $msg ' (tipo) desc
     * @param $goto ='' (tipo) desc
     * @param $caption ='' (tipo) desc
     * @param $event ='' (tipo) desc
     * @param $halt = (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public static function error($msg = '', $goto = '', $caption = '', $event = '', $halt = true)
    {
        self::$instance->prompt(MPrompt::error($msg, $goto, $caption, $event), $halt);
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $msg (tipo) desc
     * @param $goto ' (tipo) desc
     * @param $event ='' (tipo) desc
     * @param $halt = (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public static function information($msg, $goto = '', $event = '', $halt = true)
    {
        self::$instance->prompt(MPrompt::information($msg, $goto, $event), $halt);
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $msg (tipo) desc
     * @param $gotoOK ' (tipo) desc
     * @param $gotoCancel ='' (tipo) desc
     * @param $eventOk ='' (tipo) desc
     * @param $eventCancel ='' (tipo) desc
     * @param $halt = (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public static function confirmation($msg, $gotoOK = '', $gotoCancel = '', $eventOk = '', $eventCancel = '', $halt = true)
    {
        self::$instance->prompt(MPrompt::confirmation($msg, $gotoOK, $gotoCancel, $eventOk, $eventCancel), $halt);
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $msg (tipo) desc
     * @param $gotoYes ' (tipo) desc
     * @param $gotoNo ='' (tipo) desc
     * @param $eventYes ='' (tipo) desc
     * @param $eventNo ='' (tipo) desc
     * @param $halt = (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public static function question($msg, $gotoYes = '', $gotoNo = '', $eventYes = '', $eventNo = '', $halt = true)
    {
        self::$instance->prompt(MPrompt::question($msg, $gotoYes, $gotoNo, $eventYes, $eventNo), $halt);
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $prompt (tipo) desc
     * @param $halt (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public static function prompt($prompt, $halt = true)
    {
        self::$instance->getPage()->prompt($prompt);
    }

    //
    // Log, Trace, Dum, Profile
    //
    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $logname (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public static function setLog($logname)
    {
        self::$instance->getObject('log')->setLog($logname);
    }

    public static function getLog()
    {
        return self::$instance->getObject('log');
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $sql (tipo) desc
     * @param $force (tipo) desc
     * @param $conf = (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public static function logSQL($sql, $force = false, $conf = '?')
    {
        self::$instance->getObject('log')->logSQL($sql, $force, $conf);
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $error (tipo) desc
     * @param $conf (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public static function logError($error, $conf = 'maestro')
    {
        self::$instance->getObject('log')->logError($error, $conf);
    }

    /**
     * Mensagem de erro padrão para o caso da chamada de uma funcao obsoleta.
     */
    public static function logDeprecated($message = '', $throwsExceptionToDevs = false)
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        $deprecated = next($trace);
        $caller = next($trace);
        $deprecatedSignature = "{$deprecated['class']}::{$deprecated['function']}()";
        $callerSignature = "{$caller['class']}::{$caller['function']}()";

        $error = "[DEPRECATED] Chamada a funcao obsoleta $deprecatedSignature " .
            "a partir de $callerSignature em {$deprecated['file']} " .
            "linha {$deprecated['line']}";

        self::logError($error);

        if ($throwsExceptionToDevs && self::DEV()) {
            throw new \Exception("$message<BR>$error");
        }
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns (tipo) desc
     *
     */
    public static function profileTime()
    {
        return self::$instance->getObject('profile')->profileTime();
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $name (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public static function profileEnter($name)
    {
        return self::$instance->getObject('profile')->profileEnter($name);
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $name (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public static function profileExit($name)
    {
        return self::$instance->getObject('profile')->profileExit($name);
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns (tipo) desc
     *
     */
    public static function profileDump()
    {
        return self::$instance->getObject('profile')->profileDump();
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns (tipo) desc
     *
     */
    public static function getProfileDump()
    {
        return self::$instance->getObject('profile')->getProfileDump();
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $var (tipo) desc
     * @param $file (tipo) desc
     * @param $line =false (tipo) desc
     * @param $info =false (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public static function dump($var, $file = false, $line = false, $info = false)
    {
        return self::$instance->getObject('dump')->dump($var, $file, $line, $info);
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns (tipo) desc
     *
     */
    public static function isLogging()
    {
        return self::$instance->getObject('log')->isLogging();
    }

    public static function isLogged()
    {
        return self::$instance->getAuth()->isLogged();
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $msg (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public static function logMessage($msg)
    {
        return self::$instance->getObject('log')->logMessage($msg);
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $msg (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public static function deprecate($msg)
    {
        self::$instance->logMessage('[DEPRECATED]' . $msg);
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $msg (tipo) desc
     * @param $file (tipo) desc
     * @param $line =0 (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public static function trace($msg, $file = false, $line = 0)
    {
        return self::$instance->getObject('trace')->trace($msg, $file, $line);
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns (tipo) desc
     *
     */
    public static function traceDump($msg, $file = false, $line = 0, $tag = null)
    {
        return self::$instance->getObject('trace')->traceDump($msg, $file, $line, $tag);
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @returns (tipo) desc
     *
     */
    public static function traceStack()
    {
        return self::$instance->getObject('trace')->traceStack();
    }

    public static function version()
    {
        return MAESTRO_VERSION;
    }

    public static function author()
    {
        return MAESTRO_AUTHOR;
    }

    public static function getTracerStatus()
    {

        $tracerStatus = self::getConf('logs.level');
        switch ($tracerStatus) {
            case '0': {
                return 'Inativo';
            }
            case '1': {
                return 'Status 1';
            }
            case '2': {
                return 'Ativo em ' . self::getConf('logs.peer') . ":" . self::getConf('logs.port');
            }
        }
    }

    /**
     * Brief Description.
     * Complete Description.
     *
     * @param $dir (tipo) desc
     * @param $typed ' (tipo) desc
     *
     * @returns (tipo) desc
     *
     */
    public static function listFiles($dir, $type = 'd')
    {
        $result = '';
        if (is_dir($dir)) {
            $thisdir = dir($dir);
            while ($entry = $thisdir->read()) {
                if (($entry != '.') && ($entry != '..') && (substr($entry, 0, 1) != '.')) {
                    if ($type == 'a') {
                        $result[$entry] = $entry;
                        continue;
                    }
                    $isFile = is_file("$dir/$entry");
                    $isDir = is_dir("$dir/$entry");

                    if (($type == 'f') && ($isFile)) {
                        $result[$entry] = $entry;
                        continue;
                    }

                    if (($type == "d") && ($isDir)) {
                        $result[$entry] = $entry;
                        continue;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Send a file to client
     * @param string $module Module
     * @param string $filename Complete filepath relative to directory "files" on module dir
     */

    /**
     * @param string $module
     * @param string $filename
     * @param string $dir
     * @return bool
     */
    public static function saveFile($module = '', $filename = '', $dir = 'html/files/')
    {
        if (empty($filename)) {
            return false;
        }

        $path = self::$instance->getModulePath($module, $dir);
        self::$instance->response->sendDownload($path . $filename);
    }

    /**
     * Retorna uma string aleatória de 24 caracteres. Essa string será única
     * durante toda a sessão.
     *
     * @param bool $create Se true cria uma chave nova se ela não existir.
     * @return string
     */
    public static function getSessionToken($create = true)
    {
        $container = self::getSession()->container('sessionKey');
        if (!$container->key && $create) {
            $container->key = \MSSL::randomString(24);
        }

        return $container->key;
    }

    public static function postAutoloadDump(Event $event)
    {
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        $baseDir = dirname($vendorDir);
        $sysTime = self::getSysTime();
        $map = require($vendorDir . '/composer/autoload_classmap.php');
        $newMap = "<?php\n// autoload_manager.php @generated by Manager::postAutoloadDump running as a Composer script @{$sysTime}.\n\n";
        $newMap .= "\$baseDir = dirname(dirname(__FILE__));\n\n";
        $newMap .= "return array(\n";
        foreach ($map as $className => $file) {
            if (strpos($className, "\\") === false) {
                if (strpos($className, "_") === false) {
                    $className = strtolower($className);
                    //$file = realpath($file);
                    $file = str_replace($baseDir, '', $file);
                    $newMap .= "    '{$className}' => \$baseDir . '{$file}',\n";
                }
            }
        }
        $newMap .= ");";
        file_put_contents($vendorDir . '/autoload_manager.php', $newMap);
    }

    /*
    public static function postAppAutoloadDump(Event $event)
    {
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        $map = require($vendorDir . '/composer/autoload_classmap.php');
        $newMap = "<?php\n// autoload_manager.php @generated by Manager::postAppAutoloadDump running as a Composer script\n\nreturn array(\n";
        foreach ($map as $className => $file) {
            if (strpos($className, "_") === false) {
                $className = str_replace('\\', '\\\\', strtolower($className));
                //$file = realpath($file);
                $newMap .= "    '{$className}' => '{$file}',\n";
            }
        }
        $newMap .= ");";
        file_put_contents($vendorDir . '/autoload_manager.php', $newMap);
    }
    */

    public static function createFileMap(Event $event)
    {
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        var_dump($vendorDir);
        $baseDir = dirname($vendorDir);
        $baseDirSrc = $baseDir . DIRECTORY_SEPARATOR . 'src';
        $sysTime = self::getSysTime();
        $newMap = "<?php\n// autoload_manager.php @generated by Manager::createFileMap running as a Composer script @{$sysTime}.\n\n";
        if (file_exists($baseDirSrc)) {
            $baseDir = $baseDirSrc;
            $newMap .= "\$baseDir = dirname(dirname(__FILE__)) . '" . DIRECTORY_SEPARATOR . 'src' . "';\n\n";
        } else {
            $newMap .= "\$baseDir = dirname(dirname(__FILE__));\n\n";
        }
        $newMap .= "return array(\n";
        $newMap .= self::getHandlerFiles($baseDir);
        $base = $baseDir . DIRECTORY_SEPARATOR . 'modules';
        if (file_exists($base)) {
            $scandir = scandir($base) ?: [];
            $scandir = array_diff($scandir, ['..', '.']);
            foreach ($scandir as $path) {
                $module = strtolower($path);
                $newMap .= self::getHandlerFiles($base . DIRECTORY_SEPARATOR . $module, $module);
            }
        }
        $newMap .= ");";
        file_put_contents($vendorDir . '/filemap.php', $newMap);
    }

    private static function getHandlerFiles($path, $module = '')
    {
        var_dump($path);
        $map = '';
        $base = $path . DIRECTORY_SEPARATOR . 'controllers';
        if (file_exists($base)) {
            $scandir = scandir($base) ?: [];
            $scandir = array_diff($scandir, ['..', '.']);
            foreach ($scandir as $filePath) {
                $ns = strtolower(($module ? $module . '\\\\' : '') . "controllers\\\\" . basename($filePath, '.php'));
                $fullPath = "/" . ($module ? 'modules/' . $module . '/' : '') . "controllers/" . $filePath;
                $map .= "    '{$ns}' => \$baseDir . '{$fullPath}',\n";
            }
        }
        $base = $path . DIRECTORY_SEPARATOR . 'services';
        if (file_exists($base)) {
            $scandir = scandir($base) ?: [];
            $scandir = array_diff($scandir, ['..', '.']);
            foreach ($scandir as $filePath) {
                $ns = strtolower(($module ? $module . '\\\\' : '') . "services\\\\" . basename($filePath, '.php'));
                $fullPath = "/" . ($module ? 'modules/' . $module . '/' : '') . "services/" . $filePath;
                $map .= "    '{$ns}' =>  \$baseDir . '{$fullPath}',\n";
            }
        }
        $base = $path . DIRECTORY_SEPARATOR . 'components';
        if (file_exists($base)) {
            $scandir = scandir($base) ?: [];
            $scandir = array_diff($scandir, ['..', '.']);
            foreach ($scandir as $filePath) {
                if (fnmatch("*.php", $filePath)) {
                    $ns = strtolower(($module ? $module . '\\\\' : '') . "components\\\\" . basename($filePath, '.php'));
                    $fullPath = "/" . ($module ?  'modules/' . $module . '/' : '') . "components/" . $filePath;
                    $map .= "    '{$ns}' =>  \$baseDir . '{$fullPath}',\n";
                }
            }
        }
        return $map;
    }

}

spl_autoload_register(array('Manager', 'autoload'));

