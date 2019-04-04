<?php

use Maestro\Services\Exception\ESecurityException;
use \fnbr\models\Base;

class ProfileController extends MController {

    public function main() {
        $this->renderPrompt("info", "Em desenvolvimento");
    }
    
    public function formMyProfile() {
        $user = Base::getCurrentUser();
        $this->data->idUser = $user->getId();
        $this->data->languagePreference = $user->getConfigData('fnbrLangPref');
        $this->data->languages = Base::languages();
        $this->data->title = "Profile of " . $user->getLogin();
        $this->render();
    }

    public function formChangePassword() {
        $user = Base::getCurrentUser();
        $this->data->idUser = $user->getId();
        $this->data->title = "Change Password of " . $user->getLogin();
        $this->data->action= "@mfn/profile/changePassword|formChangePassword";
        $this->render();
    }

    public function myProfile()
    {
        try {
            $user = new fnbr\auth\models\User($this->data->idUser);
            $user->setConfigData('fnbrLangPref', $this->data->languagePreference);
            $this->renderPrompt('information', 'Ok');
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }

    }
    
    public function changePassword() {
        try {
            $user = new fnbr\auth\models\User($this->data->idUser);
            if (!$user->validatePassword($this->data->current)) {
                throw new ESecurityException('Wrong password!');
            }
            if ($this->data->newPassword != $this->data->newPassword2) {
                throw new ESecurityException('Passwords dont matches!');
            }
            $user->newPassword($this->data->newPassword);
            $go = "!$('#formChangePassword_dialog').dialog('close');";        
            $this->renderPrompt('information', 'Password changed!', $go);
        } catch (\Exception $e) {
            $go = "!$('#formChangePassword_dialog').dialog('close');";        
            $this->renderPrompt('error', $e->getMessage(), $go);
        }
    }
    
}