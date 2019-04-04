<?php

use fnbr\models\Base;

/**
 * Class APIDataService
 * Provida data for UI clients, using JSON
 */
class APIDataService extends MService
{

    public function getSelectionUserLevel()
    {
        $userLevel = Base::userLevel();
        $selection = [];
        foreach($userLevel as $level => $name) {
            $selection[] = (object)[
                'value' => $level,
                'text' => $level
            ];
        }
        $this->renderJSON(json_encode($selection));
    }

}
