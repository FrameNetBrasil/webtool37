<?php
/**
 *
 *
 * @category   Maestro
 * @package    UFJF
 * @subpackage
 * @copyright  Copyright (c) 2003-2012 UFJF (http://www.ufjf.br)
 * @license    http://siga.ufjf.br/license
 * @version
 * @since
 */

use fnbr\models\Base,
    fnbr\models\Language,
    fnbr\auth\models\User;
use Auth0\SDK\Auth0;

class MainController extends \MController
{

    public function init()
    {
        Manager::checkLogin(false);
    }

    public function main()
    {
        if (Manager::isLogged()) {
            $this->render('formMain');
        } else {
            if (Manager::getConf('login.handler') == 'auth0') {

                $this->data->domain = Manager::getConf('login.AUTH0_DOMAIN');
                $this->data->client_id = Manager::getConf('login.AUTH0_CLIENT_ID');
                $this->data->client_secret = Manager::getConf('login.AUTH0_CLIENT_SECRET');
                $this->data->redirect_uri = Manager::getConf('login.AUTH0_CALLBACK_URL');

                $auth0 = new Auth0([
                    'domain' => $this->data->domain,
                    'client_id' => $this->data->client_id,
                    'client_secret' => $this->data->client_secret,
                    'redirect_uri' => $this->data->redirect_uri,
                    'audience' => 'urn:test:api',
                    'persist_id_token' => true,
                    'persist_refresh_token' => true,
                ]);

                $this->data->userInfo = $auth0->getUser();
                $this->render('auth0Login');
            } else {
                $this->data->datasources = Manager::getConf('fnbr.datasource');
                $this->data->action = "@auth/login/authenticate|formLogin";
                $this->render('formLogin');
            }
        }
    }

    public function formMain()
    {
        $this->render();
    }

    public function login()
    {
        $this->data->datasources = Manager::getConf('fnbr.datasource');
        $this->data->action = "@auth/login/authenticate|formLogin";
        $this->render('formLogin');
    }

    public function logout()
    {
        Manager::getAuth()->logout();
        $main = Manager::getURL('main');
        if (Manager::getConf('login.handler') == 'auth0') {
            $main = Manager::getConf('login.logout') . urlencode(Manager::getBaseURL(true));
        }
        $this->redirect($main);
    }

    public function auth0Callback()
    {
        $goMain = "=main";//Manager::getURL('main');
        try {
            $this->data->domain = Manager::getConf('login.AUTH0_DOMAIN');
            $this->data->client_id = Manager::getConf('login.AUTH0_CLIENT_ID');
            $this->data->client_secret = Manager::getConf('login.AUTH0_CLIENT_SECRET');
            $this->data->redirect_uri = Manager::getConf('login.AUTH0_CALLBACK_URL');

            $auth0 = new Auth0([
                'domain' => $this->data->domain,
                'client_id' => $this->data->client_id,
                'client_secret' => $this->data->client_secret,
                'redirect_uri' => $this->data->redirect_uri,
                'audience' => 'urn:test:api',
                'persist_id_token' => true,
                'persist_refresh_token' => true,
            ]);

            $userInfo = $auth0->getUser();
            $user = Manager::getAppService('authuser');
            $status = $user->auth0Login($userInfo);
            if ($status == 'new') {
                $this->renderPrompt('info', _M('User registered. Now it is necessary Administrator approval.'), $goMain);
            } elseif ($status == 'pending') {
                $this->renderPrompt('info', _M('User already registered, but waiting for Administrator approval.'), $goMain);
            } elseif ($status == 'logged') {
                $this->redirect(Manager::getURL('main'));
            } else {
                $this->renderPrompt('error', _M('Login failed; contact administrator.', $goMain));
            }
        } catch (Exception $e) {
			mdump($e->getMessage());
            $this->renderPrompt('error', "Auth0: Invalid authorization code.", $goMain);
        }
    }

    public function changeLanguage()
    {
        $idLanguage = Base::getIdLanguage($this->data->id);
        Manager::getSession()->idLanguage = $idLanguage;
        $this->redirect(Manager::getURL('main'));
    }

    public function changeLevel()
    {
        $login = Manager::getLogin();
        $toLevel = $this->data->id;
        $user = $login->getUser();
        $levels = $user->getAvaiableLevels();
        if ($levels[$toLevel]) {
            $newUser = new fnbr\auth\models\User($levels[$toLevel]);
            $login->setUser($newUser);
            Manager::getSession()->mfnLayers = $newUser->getConfigData('fnbrLayers');
            Manager::getSession()->mfnLevel = $toLevel;
            $this->redirect(Manager::getURL('main'));
        } else {
            $this->renderPrompt('error', _M('You don\'t have such level.'));
        }

    }

    public function jcryption()
    {
        $path = Manager::getAppPath('conf');
        $pathPUB = $path . '/rsa_1024_pub.pem';
        $pathPVT = $path . '/rsa_1024_priv.pem';
        $jc = new fnbr\models\jcryption($pathPUB, $pathPVT);
        $jc->go();
        header('Content-type: text/plain');
        print_r($_POST);
        die();
    }
}

