<?php

class Menu extends MMenuBar
{

    public function __construct()
    {
        parent::__construct('mmenu');
        $this->setId('fnbrMainMenu');
        $actions = Manager::getActions('fnbr');
        foreach ($actions as $i => $group) {
            if (($i == 'profile') || ($i == 'language')) {
                continue;
            }
            mdump($i);
            $hasAccessGroup = ($group[ACTION_ACCESS] == '') || Manager::checkAccess($group[ACTION_TRANSACTION], $group[ACTION_ACCESS]);
            if ($hasAccessGroup) {
                $menuBarItem[$i] = new MMenuBarItem(array("id" => "menu{$i}", "label" => _M($group[ACTION_CAPTION]), "iconCls" => $group[ACTION_ICON]));
                $groupActions = $group[ACTION_ACTIONS];
                $menu = new MMenu(array("id" => "mmenu{$i}"));
                foreach ($groupActions as $j => $action) {
                    $hasAccessAction = ($action[ACTION_ACCESS] == '') || Manager::checkAccess($action[ACTION_TRANSACTION], $action[ACTION_ACCESS]);
                    if ($hasAccessAction) {
                        $handler = MAction::isAction($action[ACTION_PATH]) ? $action[ACTION_PATH] : '>' . $action[ACTION_PATH];
                        $menuItem = new MMenuItem(array("id" => "menu{$i}{$j}", "text" => _M($action[ACTION_CAPTION]), "action" => $handler, "iconCls" => $action[ACTION_ICON]));
                        $menu->addControl($menuItem);
                    }
                }
                $menuBarItem[$i]->addControl($menu);
                $this->addControl($menuBarItem[$i]);
            }
        }
    }

}

