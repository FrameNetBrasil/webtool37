<?php


class StructureConstraintInstanceService extends MService
{
    public function listConstraintsFE($idFrameElement)
    {
        $result = [];
        $fe = new fnbr\models\FrameElement($idFrameElement);
        $constraints = $fe->listConstraints();
        mdump($constraints);
        foreach ($constraints as $constraint) {
            $node = [];
            $node['id'] = 'x' . strtolower($constraint['type']) . '_' . $fe->getIdEntity() . '_' . $constraint['idConstraint'];
            $node['text'] = $constraint['name'];
            $node['state'] = 'closed';
            $node['iconCls'] = 'icon-blank fa-icon fa fa-crosshairs';
            $result[] = $node;
        }
        return json_encode($result);
    }

    public function listConstraintsLU($idLU)
    {
        $result = [];
        $lu = new fnbr\models\LU($idLU);
        $constraints = $lu->listConstraints();
        foreach ($constraints as $constraint) {
            $node = [];
            $node['id'] = 'y' . $lu->getIdEntity() . '_' . $constraint['idConstraint'];
            $node['text'] = $constraint['name'];
            $node['state'] = 'closed';
            $node['iconCls'] = 'icon-blank fa-icon fa fa-crosshairs';
            $result[] = $node;
        }
        return $result;
    }

    public function listConstraintsCE($idConstructionElement)
    {
        $result = [];
        $ce = new fnbr\models\ConstructionElement($idConstructionElement);
        $constraints = $ce->listConstraints();
        mdump($constraints);
        foreach ($constraints as $constraint) {
            $node = [];
            $node['id'] = 'x' . $constraint['idConstraint'];
            $node['text'] = $constraint['name'];
            $node['state'] = 'closed';
            $node['iconCls'] = 'icon-blank fa-icon fa fa-crosshairs';
            $result[] = $node;
        }
        return json_encode($result);
    }

    public function listConstraintsCN($idConstraint)
    {
        $result = [];
        $constraint = new fnbr\models\ConstraintInstance($idConstraint);
        $constraints = $constraint->listConstraints();
        foreach ($constraints as $constraint) {
            $node = [];
            $node['id'] = 'x' . $constraint['idConstraint'];
            $node['text'] = $constraint['name'];
            $node['state'] = 'closed';
            $node['iconCls'] = 'icon-blank fa-icon fa fa-crosshairs';
            $result[] = $node;
        }
        return json_encode($result);
    }

    public function listConstraintsCNCN($idConstraint)
    {
        $result = [];
        $constraint = new fnbr\models\ConstraintInstance($idConstraint);
        $constraints = $constraint->listConstraintsCN();
        foreach ($constraints as $constraint) {
            $node = [];
            $node['id'] = 'z' . $constraint['idConstraint'];
            $node['text'] = $constraint['name'];
            $node['state'] = 'closed';
            $node['iconCls'] = 'icon-blank fa-icon fa fa-crosshairs';
            $result[] = $node;
        }
        return json_encode($result);
    }

    public function listConstraintsCX($idCxn)
    {
        $result = [];
        $cxn = new fnbr\models\Construction($idCxn);
        $constraints = $cxn->listConstraints();
        mdump($constraints);
        foreach ($constraints as $constraint) {
            $node = [];
            $node['id'] = 'n' . $constraint['idConstraint'];
            $node['text'] = $constraint['name'];
            $node['state'] = 'closed';
            $node['iconCls'] = 'icon-blank fa-icon fa fa-crosshairs';
            $result[] = $node;
        }
        return json_encode($result);
    }

    public function listEvokesCX($idCxn)
    {
        $result = [];
        $cxn = new fnbr\models\Construction($idCxn);
        $evokes = $cxn->listEvokesRelations();
        foreach($evokes as $evoke) {
            foreach ($evoke as $evk) {
                $node = [];
                $node['id'] = 'v' . $evk['idEntity'];
                $node['text'] = 'evk_' . $evk['name'];
                $node['state'] = 'closed';
                $node['iconCls'] = 'icon-blank fa-icon fa fa-crosshairs';
                $result[] = $node;
            }
        }
        return json_encode($result);
    }

    public function listEvokesCE($idCE)
    {
        $result = [];
        $ce = new fnbr\models\ConstructionElement($idCE);
        $evokes = $ce->listEvokesRelations();
        foreach($evokes as $evoke) {
            foreach ($evoke as $evk) {
                $node = [];
                $node['id'] = 'v' . $evk['idEntity'];
                $node['text'] = 'evk_' . $evk['name'];
                $node['state'] = 'closed';
                $node['iconCls'] = 'icon-blank fa-icon fa fa-crosshairs';
                $result[] = $node;
            }
        }
        return json_encode($result);
    }

    public function constraintHasChild($idConstraintInstance)
    {
        $constraint = new fnbr\models\ViewConstraint();
        return $constraint->hasChild($idConstraintInstance);
    }

}
