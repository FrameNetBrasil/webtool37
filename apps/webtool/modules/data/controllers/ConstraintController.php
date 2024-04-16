<?php

class ConstraintController extends MController
{

    public function lookupDataByCE()
    {
        //$constraint = new fnbr\models\ConstraintInstance($this->data->id);
        $constraint = new fnbr\models\ConstraintInstance();
        $constraint->getByIdConstraint($this->data->id);
        $constraintData = $constraint->getConstraintData();
        //$constraint->setIdEntity($constraintData->idConstrainedBy);
        //$constraints = $constraint->listConstraints();
        $constraints = $constraint->listByIdConstrained($constraintData->idConstrainedBy);
        $data = [];
        $cxn = new fnbr\models\Construction();
        foreach ($constraints as $cn) {
            if ($cn['relationType'] == 'con_cxn') {
                $idConstruction = $cn['idConstrainedBy'];
                $cxn->getByIdEntity($idConstruction);
                $data[] = [
                    'idConstruction' => $cxn->getId(),
                    'name' => $cxn->getName()
                ];
                $heiress = [];
                $cxn->listAllHeiress($cxn->getId(), $heiress);
                foreach($heiress as $heir) {
                    $data[] = [
                        'idConstruction' => $heir['idConstruction'],
                        'name' => $heir['name']
                    ];
                }
                /*
                $daughters = $cxn->listDaughterRelations();
                mdump('### daughters');
                mdump($daughters);
                foreach($daughters as $daughter) {
                    $data[] = [
                        'idConstruction' => $daughter['idConstruction'],
                        'name' => $daughter['name']
                    ];
                }
                */
                /*
                $constraint->getById($constraintData->idConstrained);
                $constraintData = $constraint->getConstraintData();
                $constraint->setIdEntity($constraintData->idConstrained);
                $upperConstraints = $constraint->listConstraints();
                foreach ($upperConstraints as $upperCn) {
                    if ($upperCn['idConstrainedBy'] == $idConstruction) {
                        $data[] = [
                            'idConstruction' => $upperCn['idConstraint'],
                            'name' => $upperCn['name']
                        ];
                    }
                }
                */
            }

        }
        mdump($data);
        mdump('==');
        $this->renderJSON($constraint->gridDataAsJSON($data));
    }

    public function lookupDataConstruction()
    {
        $entry = new fnbr\models\Entry();
        $filter = (object)[
            'entries' => ['rel_constraint_before'],
            'idLanguage' => \Manager::getSession()->idLanguage
        ];
        $data = [];
        $result = $entry->listByFilter($filter)->asQuery()->getResult();
        foreach($result as $constraint) {
            $data[] = [
                'entry' => $constraint['entry'],
                'name' => $constraint['name']
            ];
        }
        mdump($data);
        $this->renderJSON($entry->gridDataAsJSON($data));
    }

}