<?php

use fnbr\auth\models\User;

class AuthUserService extends MService
{
    public function auth0Login($userInfo)
    {
        $userData = new PlainObject([
            'auth0IdUser' => $userInfo['user_id'],
            'email' => $userInfo['email'],
            'auth0CreatedAt' => $userInfo['created_at'],
            'name' => $userInfo['name'],
            'nick' => $userInfo['nickname']
        ]);
        mdump($userData);
        $user = new User();
        $result = $user->listByFilter($userData)->asQuery()->getResult();
        if (count($result) == 0) {
            $user->createUser($userData);
            return 'new';
        } else {
            $user->getById($result[0]['idUser']);
            if ($user->getStatus() == '0') {
                return 'pending';
            } else {
                $user->registerLogin();
                $login = new MLogin($user);
                $auth = new MAuth();
                $auth->setLogin($login);
                $auth->setLoginLogUserId($user->getId());
                $auth->setLoginLog($login->getLogin());

                $idLanguage = $user->getConfigData('fnbrIdLanguage');
                if ($idLanguage == '') {
                    $idLanguage = 1;
                    $user->setConfigData('fnbrIdLanguage', $idLanguage);
                }
                Manager::getSession()->idLanguage = $idLanguage;
                Manager::getSession()->fnbrLevel = $user->getUserLevel();

                Manager::logMessage("[LOGIN] Authenticated {$user->getLogin()}");
                return 'logged';
            }
        }
    }

    public function listForTree($filter)
    {
        $user = new User();
        $users = $user->listByFilter($filter)->asQuery()->getResult(\FETCH_ASSOC);
        $result = [];
        foreach ($users as $row) {
            $node = [
                'id' => 'u' . $row['idUser'],
                'text' => $row['login'],
                'state' => 'closed',
                'iconCls' => 'icon-man',
                'entry' => $row['entry']
            ];
            $result[] = $node;
        }
        return $result;

    }

}
