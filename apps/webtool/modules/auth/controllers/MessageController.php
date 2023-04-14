<?php

Manager::import("fnbr\auth\models\*");

class MessageController extends MController {

    private $idLanguage;
        
    public function init()
    {
        parent::init();
        $this->idLanguage = Manager::getConf('options.language');
        $msgDir = Manager::getAppPath('conf/report');
        Manager::$msg->file = 'messages.' . $this->idLanguage . '.php';
        Manager::$msg->addMessages($msgDir);
    }
    
    public function main() {
        $this->render("formBase");
    }

    public function formMail(){
        $user = new fnbr\auth\models\User();
        $this->data->users = $user->listByFilter()->asQuery()->chunkResult('idUser','name');
        $group = new fnbr\auth\models\Group();
        $this->data->groups = $group->listByFilter()->asQuery()->chunkResult('idGroup','name');
        $this->data->send = "@auth/message/mail|formMail";
        $this->render();
    }
    
    public function mail() {
        try {
            $emailService = Manager::getAppService('email');
            $to = [];
            if ($this->data->toUser != '') {
                $user = new fnbr\auth\models\User($this->data->toUser);
                $email = $user->getPerson()->getEmail(); 
                $to[$email] = $email;
            }
            if ($this->data->toGroup != '') {
                $group = new fnbr\auth\models\Group($this->data->toGroup);
                $users = $group->getUsers();
                foreach ($users as $user) {
                    $email = $user->getPerson()->getEmail(); 
                    $to[$email] = $email;
                }
            }
            $fromUser = \fnbr\models\Base::getCurrentUser();
            $from = (object)[
                'from' => $fromUser->getPerson()->getEmail(),
                'fromName' => $fromUser->getPerson()->getName(),
            ];
            $emailService->sendEmailThroughSystem($from, $to, $this->data->subject, $this->data->body);
            $this->renderPrompt('information', 'Ok');
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }    
}