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
class MController
{

    private $logger;
    private $encryptedFields = array();
    protected $name;
    protected $application;
    protected $module;
    protected $action;
    protected $data;
    protected $params;
    public $renderArgs = array();

    public function __construct()
    {

    }

    public function __call($name, $arguments)
    {
        if (!is_callable($name)) {
            throw new \BadMethodCallException("Method [{$name}] doesn't exists in " . get_class($this) . " Controller.");
        }
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setApplication($application)
    {
        $this->application = $application;
    }

    public function getApplication()
    {
        return $this->application;
    }

    public function setModule($module)
    {
        $this->module = $module;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setEncryptedFields(array $fields)
    {
        $this->encryptedFields = $fields;
    }

    public function isPost()
    {
        return Manager::getContext()->isPost();
    }

    public function init()
    {
        Manager::checkLogin();
    }

    public function dispatch($action)
    {
        mtrace('mcontroller::dispatch = ' . $action);
        $this->decryptData();

        if (!method_exists($this, $action)) {
            mtrace('action does not exists = ' . $action);
            try {
                $this->render($action);
            } catch (Exception $e) {
                throw new ERunTimeException(_M("App: [{$this->application}], Module: [{$this->module}], Controller: [{$this->name}] : action [{$action}] not found!"));
            }
        } else {
            $this->action = $action;
            if ($this->isPost()) {
                $actionPost = $action . 'Post';
                if (method_exists($this, $actionPost)) {
                    $action = $actionPost;
                }
            }
            $this->callAction($action);
        }
    }

    private function callAction($action)
    {
        mtrace('executing = ' . $action);
        try {
            $method = new \ReflectionMethod(get_class($this), $action);
            $params = $method->getParameters();
            $values = array();
            foreach ($params as $param) {
                $value = $this->data->{$param->getName()};
                if (!$value && $param->isDefaultValueAvailable()) {
                    $value = $param->getDefaultValue();
                }
                $values[] = $value;
            }
            $result = call_user_func_array([$this, $action], $values);

            if (!$this->getResult()) {
                if (!Manager::isAjaxCall()) {
                    Manager::$ajax = new MAjax(Manager::getOptions('charset'));
                }
                $format = Manager::getContext()->getResultFormat();
                if ($format == 'JSON') {
                    if (!is_json($result)) {
                        $result = json_encode($result);
                    }
                    $this->setResult(new MRenderJSON($result));
                } elseif ($format == 'TXT') {
                    $this->setResult(new MRenderText($result));
                } else {
                    $this->setResult(new MRenderText($result));
                }
            }
        } catch (\EModelException $e) {
            $this->renderDefaultAlert($e->getMessage());
        } catch (\EControllerException $e) {
            $this->renderDefaultAlert($e->getMessage());
        } catch (\ESecurityException $e) {
            $this->renderAccessError($e->getMessage());
        } catch (\Exception $e) {
            $this->renderUnexpectedError($e, $action);
        }
    }

    private function renderDefaultAlert($msg)
    {
        mdump('Controller::dispatch exception: ' . $msg);
        $this->renderPrompt('alert', $msg);
    }

    private function renderAccessError($msg)
    {
        mdump('Controller::dispatch exception: ' . $msg);
        $this->renderPrompt('error', $msg, 'main/main');
    }

    private function renderUnexpectedError(\Exception $e, $action)
    {
        if (\Manager::PROD()) {
            $this->renderPrompt('error', 'Ocorreu um erro inesperado!', 'main/main');
        } else {
            $this->renderPrompt('error', "[<b>" . $this->name . '/' . $action . "</b>]" . $e->getMessage());
        }

        $msg = "{$e->getFile()}({$e->getLine()}): {$e->getMessage()}";

        if (Manager::getLogin()) {
            $msg .= ' idUser = ' . Manager::getLogin()->getIdUser() . ', profile = ' . Manager::getLogin()->getProfile();
        }

        mdump('Controller::dispatch exception: ' . $e->getMessage());
        \Manager::logError($msg);
    }

    /**
     * Executed at the end of Controller execution.
     */
    public function terminate()
    {

    }

    public function forward($action)
    {
        Manager::getFrontController()->setForward($action);
    }

    public function setResult($result)
    {
        Manager::getFrontController()->setResult($result);
    }

    public function getResult()
    {
        return Manager::getFrontController()->getResult();
    }

    public function getContainer()
    {
        return Manager::getFrontController()->getContainer();
    }

    public function setParams($params)
    {
        $this->params = $params;
    }

    protected function setProperty($property, $value, $fields)
    {
        foreach ($fields as $field) {
            $this->data->{$field . $property} = $value;
        }
    }

    public function getParams()
    {
        return $this->params;
    }

    public function setData()
    {
        $this->data = Manager::getData();
    }

    public function setDataObject($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    private function getParameters($parameters = NULL)
    {
        if (!(is_object($parameters) || is_array($parameters))) {
            $parameters = array('result' => $parameters);
        }
        foreach ($parameters as $name => $value) {
            $this->data->$name = $value;
        }
    }

    public function getService($service, $module = '')
    {
        $service = Manager::getService($this->application, ($module == '' ? $this->module : $module), $service);
        $service->setData();
        return $service;
    }

    /**
     * A partir do nome do controller e do nome da view, constrói o path completo do arquivo da view.
     * Executa renderView para obter o conteúdo a ser passado para uma classe Result.
     * @param $controller string Nome do controller
     * @param $view string Nome da view
     * @param null $parameters object Objeto Data
     * @return string Conteudo a ser passado para uma classe Result
     */
    private function getContent($controller, $view, $parameters = NULL)
    {
        $app = $this->getApplication();
        $module = $this->getModule();
        $base = Manager::getAppPath('', $module, $app);
        $path = '/views/' . $controller . '/' . $view;
        $extensions = ['.xml', '.php', '.html', '.js', '.wiki'];
        $content = '';
        foreach ($extensions as $extension) {
            $fileName = $base . $path . $extension;
            if (file_exists($fileName)) {
                mtrace('MController::getContent ' . $fileName);
                $content = $this->renderView($controller, $fileName, $parameters);
                break;
            } else {
                $fileName = $base . Manager::getConf("srcPath.{$this->module}") . $path . $extension;
                if (file_exists($fileName)) {
                    mtrace('MController::getContent ' . $fileName);
                    $content = $this->renderView($controller, $fileName, $parameters);
                    break;
                }
            }
        }
        return $content;
    }

    /**
     * Obtem o conteúdo da view e passa para uma classe Result:
     * - MRenderJSON se for uma chamada Ajax
     * - MRenderPage se for uma chamada não-Ajax (um GET via browser)
     * @param string $viewName Nome da view. Se não informado, assume que é o nome da action.
     * @param array $parameters Objeto Data.
     */
    public function render($viewName = '', $parameters = array())
    {
        $this->encryptData();
        $content = $this->renderContent($viewName, $parameters);
        if (Manager::isAjaxCall()) {
            if (Manager::getContext()->isFileUpload()) {
                $this->setResult(new MRenderText($content));
            } else {
                $type = strtoupper(Manager::getAjax()->getResponseType());
                if ($type == 'JSON') {
                    $json = json_encode($content);
                    $this->setResult(new MRenderJSON($json));
                } else {
                    $method = Manager::getContext()->getMethod();
                    if ($method == 'GET') {
                        $this->setResult(new MRenderText($content));
                    } else {
                        $this->setResult(new MRenderJSONText($content));
                    }
                }
            }
        } else {
            $this->setResult(new MRenderPage($content));
        }
    }

    /**
     * Obtém o conteúdo da view.
     * @param string $viewName Nome da view. Se não informado, assume que é o nome da action. Opcionalmente pode incluir o nome do controller no formato <controller>/<view>.
     * @param array $parameters Objeto Data.
     * @return string Conteúdo da View.
     */
    public function renderContent($viewName = '', $parameters = array())
    {
        $controller = strtolower($this->name);
        $view = $viewName;
        if ($view == '') {
            $view = $this->action;
        } else if (strpos($view, '/') !== false) {
            $controller = substr($view, 0, strrpos($view, "/"));
            $view = substr($view, strrpos($view, "/"));
        }
        $this->getParameters($parameters);
        $content = $this->getContent($controller, $view, $this->data);
        return $content;
    }

    /**
     * Obtém o conteúdo da view a partir de uma aplicação. É esperado que a aplicação defina uma clase MView
     * que estende de MBaseView, para processar o arquivo da view e retornar o contéudo a ser passado para
     * uma classe Result.
     * @param string $app Nome da aplicação.
     * @param string $module Nome do módulo.
     * @param string $controller Nome do controller.
     * @param string $viewFile Arquivo da view.
     * @param object $parameters Objeto data.
     * @return string Conteúdo da View.
     */
    public function renderAppView($app, $module, $controller, $viewFile, $parameters)
    {
        $view = Manager::getView($app, $module, $controller, $viewFile);
        //$view->setArgs($parameters);
        $content = $view->process($this, $parameters);
        return $content;
    }

    /**
     * Obtém o conteúdo da view para a aplicação/modulo corrente.
     * @param string $controller Nome do controller.
     * @param string $viewFile Arquivo da view.
     * @param object $parameters Objeto data.
     * @return string Conteúdo da View.
     */
    public function renderView($controller, $viewFile, $parameters = array())
    {
        return $this->renderAppView($this->application, $this->module, $controller, $viewFile, $parameters);
    }

    /**
     * Instancia um template existente na pasta views e passa para a classe Result MRenderTemplate.
     * @param string $templateName Nome do template.
     * @param array $parameters Objeto Data.
     * @throws ENotFoundException Caso o template não exista.
     */
    public function renderTemplate($templateName, $parameters = array())
    {
        $controller = strtolower($this->name);
        $path = Manager::getBasePath('/views/' . $controller . '/', $this->module);
        $file = $templateName . '.html';
        if (file_exists($path . '/' . $file)) {
            $template = new MTemplate($path);
            $template->load($file);
            $this->getParameters($parameters);
            $this->setResult(new MRenderTemplate($template, $this->data));
        } else {
            throw new ENotFoundException('Template ' . $templateName . ' was not found!');
        }
    }

    /**
     * Envia um objeto MPromptData para a classe Result MRenderPrompt. É esperado que a aplicação defina uma clase MView
     * que estende de MBaseView, para pré-processar o objeto MPromptData e gerar seu conteúdo.
     * @param string|object $type String com o tipo de prompt, ou um objeto que será processado pela aplicação para gerar o conteúdo do prompt.
     * @param string $message Messagem do prompt.
     * @param string $action1 Ação para o botão do prompt.
     * @param string $action2 Ação para o botão do prompt.
     * @throws ERuntimeException Caso o parâmetro type não seja um string ou objeto.
     */
    public function renderPrompt($type, $message = '', $action1 = '', $action2 = '')
    {
        if (is_string($type)) {
            $prompt = new MPromptData($type, $message, $action1, $action2);
        } elseif (is_object($type)) {
            $prompt = new MPromptData();
            $prompt->setObject($type);
        } else {
            throw new ERuntimeException("Invalid parameter for MController::renderPrompt.");
        }
        $view = Manager::getView();
        $view->processPrompt($prompt);
        if (Manager::isAjaxCall()) {
            $type = strtoupper(Manager::getAjax()->getResponseType());
            if ($type != 'JSON') {
                $this->setResult(new MRenderText($prompt->getContent()));
            } else {
                $this->setResult(new MRenderPrompt($prompt));
            }
        } else {
            $this->setResult(new MRenderPage($prompt->getContent()));
        }
    }

    /**
     * Preenche o objeto MAjax com os dados do controller corrent (objeto Data) para seu usado pela classe Result MRenderJSON.
     * @param string $json String JSON opcional.
     */
    public function renderJSON($json = '')
    {
        if (!Manager::isAjaxCall()) {
            Manager::$ajax = new MAjax();
            Manager::$ajax->initialize(Manager::getOptions('charset'));
        }
        $ajax = Manager::getAjax();
        $ajax->setData($this->data);
        $this->setResult(new MRenderJSON($json));
    }

    /**
     * Envia um objeto JSON como resposta para o cliente.
     * Usado quando o cliente faz uma chamada AJAX diretamente e quer tratar o retorno.
     * @param $status string 'ok' ou 'error'.
     * @param $message string Mensagem para o cliente.
     * @param string $code Codigo de erro a ser interpretado pelo cliente.
     */
    public function renderResponse($status, $message, $code = '000')
    {
        $response = (object)[
            'status' => $status,
            'message' => $message,
            'code' => $code
        ];
        $this->setResult(new MRenderJSON(json_encode($response)));
    }

    /**
     * @param string $viewName
     * @param array $parameters
     */
    public function renderPartial($viewName = '', $parameters = array())
    {
        if (($view = $viewName) == '') {
            $view = $this->action;
        }
        $this->getParameters($parameters);
        $controller = strtolower($this->name);
        $this->getContent($controller, $view, $this->data);
    }

    /**
     * Processa o conteudo da view e abre nova janela/tab do browser através da classe Result MBrowserWindow.
     * É esperado que a aplicação defina uma clase MView, que estende de MBaseView, que forneça a url a ser usada.
     * @param string $viewName Nome da view.
     * @param array $parameters Objeto Data.
     */
    public function renderWindow($viewName = '', $parameters = array())
    {
        $this->renderContent($viewName, $parameters);
        $view = Manager::getView();
        $url = $view->processWindow();
        $this->setResult(new MBrowserWindow($url));
    }

    /**
     * Download de arquivo via browser.
     * @param MFile $file Arquivo a ser enviado para o browser.
     */
    public function renderFile(MFile $file)
    {
        //Manager::getPage()->window($file->getURL());
        $this->setResult(new MBrowserFile($file));
    }

    /**
     * Renderiza um stream binário inline através da classe Result MRenderBinary.
     * @param $stream Stream binário.
     */
    public function renderStream($stream)
    {
        $this->setResult(new MRenderBinary($stream, true, 'raw'));
    }

    /**
     * Renderiza um stream binário inline através da classe Result MRenderBinary, opcionalmente usando um nome de arquivo.
     * @param $stream Stream binário.
     * @param string $fileName Nome do arquivo.
     */
    public function renderBinary($stream, $fileName = '')
    {
        $this->setResult(new MRenderBinary($stream, true, $fileName));
    }

    /**
     * Download de arquivo através da classe Result MRenderBinary.
     * @param string $filePath Path do arquivo para download.
     * @param string $fileName Nome do arquivo a ser exibido para o usuário do browser.
     */
    public function renderDownload($filePath, $fileName = '')
    {
        $this->setResult(new MRenderBinary(null, false, $fileName, $filePath));
    }

    /**
     * Prepara processo de envio via flush.
     */
    public function prepareFlush()
    {
        Manager::getFrontController()->getResponse()->prepareFlush();
    }

    /**
     * Envia conteúdo para o browser via flush.
     * @param $output Conteúdo a ser enviado.
     */
    public function flush($output)
    {
        Manager::getFrontController()->getResponse()->sendFlush($output);
    }

    /**
     * Envia conteúdo da view via flush.
     * @param string $viewName Nome da view.
     * @param array $parameters Objeto data.
     */
    public function renderFlush($viewName = '', $parameters = array())
    {
        Manager::getPage()->clearContent();
        $this->renderContent($viewName, $parameters);
        $output = Manager::getPage()->generate();
        $this->flush($output);
    }

    /**
     * Redireciona browser para outra URL.
     * @param $url URL
     */
    public function redirect($url)
    {
        $view = Manager::getView();
        $content = $view->processRedirect($url);
        mdump('---');
        mdump($content);
        $this->setResult(new MRedirect(NULL, $content));
    }

    /**
     * Renderiza erro de NotFound.
     * @param $msg Mensagem a ser exibida.
     */
    public function notfound($msg)
    {
        $this->setResult(new MNotFound($msg));
    }

    protected function log($message, $operation = 'default')
    {
        if ($this->logger === null) {
            $this->logger = Manager::getModelMAD('log');
        }

        $idUser = \Manager::getLogin() ? \Manager::getLogin()->getIdUser() : 0;
        $message .= ' - IP: ' . MUtil::getClientIP();
        $this->logger->log($operation, get_class($this), 0, $message, $idUser);
    }

    /**
     * Vasculha o $this->data para encontrar campos que precisam ser criptografados.
     */
    private function encryptData()
    {
        $this->cryptIterator(function ($plain, $token) {
            return \MSSL::simmetricEncrypt($plain, $token);
        });
    }

    /**
     * Vasculha o $this->data para encontrar campos que precisam ser descriptografados.
     */
    private function decryptData()
    {
        if (!\Manager::getRequest()->getIsPostRequest()) {
            return;
        }

        $this->cryptIterator(function ($encrypted, $token) {
            return \MSSL::simmetricDecrypt($encrypted, $token);
        });
    }

    /**
     * Função que itera o $this->encryptedFields e encontra os campos que devem ser criptografados ou decriptografados.
     * @param \Closure $function
     * @throws \ESecurityException
     */
    private function cryptIterator(\Closure $function)
    {
        $token = \Manager::getSessionToken();

        foreach ($this->encryptedFields as $field) {
            if (isset($this->data->{$field})) {
                $result = $function($this->data->{$field}, $token);

                if ($result === false) {
                    throw new \ESecurityException("[cryptError]{$this->getName()}Controller::{$field}");
                }

                $this->data->{$field} = $result;
            }
        }
    }

}
