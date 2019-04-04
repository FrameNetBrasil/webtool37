<?php


class StructureConstraintTypeService extends MService
{

    public function listConstraints($filter, $idLanguage)
    {
        $result = [];
        $cn = new fnbr\models\ConstraintType();
        $constraints = $cn->listByFilter($filter)->asQuery()->getResult(\FETCH_ASSOC);
        foreach ($constraints as $constraint) {
            $node = [];
            $node['id'] = 'c' . $constraint['idConstraintType'];
            $node['text'] = $constraint['name'];
            $node['state'] = 'closed';
            $node['iconCls'] = 'icon-blank fa-icon fa fa-crosshairs';
            $result[] = $node;
        }
        return $result;
    }

}
