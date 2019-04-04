<?php

class ConstraintController extends MController
{

    public function lookupDataByCE()
    {
        $constraint = new fnbr\models\ConstraintType($this->data->id);
        $constraintData = $constraint->getConstraintData();
        $constraint->setIdEntity($constraintData->idConstrainedBy);
        $constraints = $constraint->listConstraints();
        mdump($constraints);
        $data = [];
        $cxn = new fnbr\models\Construction();
        foreach ($constraints as $cn) {
            if ($cn['relationType'] == 'rel_constraint_cxn') {
                $idConstruction = $cn['idConstrainedBy'];
                $cxn->getByIdEntity($idConstruction);
                $data[] = [
                    'idConstruction' => $cxn->getId(),
                    'name' => $cxn->getName()
                ];
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
            }

        }
        mdump($data);
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