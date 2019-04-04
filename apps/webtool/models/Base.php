<?php

namespace fnbr\models;

use fnbr\auth\models\Group;
/**
  Base class to host "criteria pieces" and methods shared by most of Models.
 */
class Base {

    static public function languages() {
        $language = new Language();
        $languages = $language->getCriteria()->select("idLanguage, language")->asQuery()->chunkResult('idLanguage', 'language');
        return $languages;
    }

    static public function languagesDescription() {
        $language = new Language();
        $languages = $language->getCriteria()->select("idLanguage, language, description")->asQuery()->treeResult('idLanguage', 'language,description');
        return $languages;
    }

    static public function languagesAsJson() {
        $language = new Language();
        $languages = $language->gridDataAsJSON($language->getCriteria()->select("idLanguage, language"), true);
        return $languages;
    }

    static public function getIdLanguage($lang) {
        $language = new Language();
        $languages = $language->getCriteria()->select("language,idLanguage")->asQuery()->chunkResult('language', 'idLanguage');
        return $languages[$lang];
    }

    // Get the Entry for current language for a giv Model
    static public function getEntry($model) {
        $entry = new Entry();
        $idLanguage = \Manager::getSession()->idLanguage;
        $filter = (object) ['entry' => $model->getEntry(), 'idLanguage' => $idLanguage];
        $criteria = $entry->listByFilter($filter);
        return $criteria->asQuery()->asObjectArray()[0];
    }

    static public function entryLanguage($criteria, $association = '') {
        $idLanguage = \Manager::getSession()->idLanguage;
        if ($association == '') {
            $associationCriteria = $criteria->getAssociation('entries');
            $alias = ($associationCriteria ? $associationCriteria->getAlias() : '');
            $associationName = ($alias == '' ? 'entries' : $alias);
            $criteria->where("{$associationName}.idLanguage = {$idLanguage}");
        } else {
            $associationName = (substr($association, -1) == '.') ? $association : "{$association}.entries.";
            $criteria->where("{$associationName}idLanguage = {$idLanguage}");
        }
    }

    static public function relation($criteria, $className1, $className2, $relationEntry) {
        $cn1 = explode(' ', $className1);
        \MUtil::SetIfNull($cn1[1], $className1);
        $cn2 = explode(' ', $className2);
        \MUtil::SetIfNull($cn2[1], $className2);
        $alias = '_' . $cn1[1] . '_' . $cn2[1];
        $criteria->join($className1, "EntityRelation er{$alias}", "{$cn1[1]}.idEntity = er{$alias}.idEntity1");
        $criteria->join("EntityRelation er{$alias}", "RelationType rt{$alias}", "er{$alias}.idRelationType = rt{$alias}.idRelationType");
        $criteria->join("EntityRelation er{$alias}", $className2, "er{$alias}.idEntity2 = {$cn2[1]}.idEntity");
        $criteria->where("rt{$alias}.entry = '{$relationEntry}'");
    }

    static public function relationCriteria($className1, $className2, $relationEntry, $select = '*') {
        $er = new EntityRelation();
        $criteria = $er->getCriteria();
        $criteria->select($select);
        $cn1 = explode(' ', $className1);
        \MUtil::SetIfNull($cn1[1], $className1);
        $cn2 = explode(' ', $className2);
        \MUtil::SetIfNull($cn2[1], $className2);
        $alias = '_' . $cn1[1] . '_' . $cn2[1];
        $criteria->join($className1, "EntityRelation er{$alias}", "{$cn1[1]}.idEntity = er{$alias}.idEntity1");
        $criteria->join("EntityRelation er{$alias}", "RelationType rt{$alias}", "er{$alias}.idRelationType = rt{$alias}.idRelationType");
        $criteria->join("EntityRelation er{$alias}", $className2, "er{$alias}.idEntity2 = {$cn2[1]}.idEntity");
        $criteria->where("rt{$alias}.entry = '{$relationEntry}'");
        return $criteria;
    }

    static public function createEntity($type, $prefix) {
        $entity = new Entity();
        do {
            $alias = $prefix . '_' . substr(uniqid(), -6);
            $entity->getByAlias($alias);
        } while ($entity->getIdEntity() != '');
        $entity->setAlias($alias);
        $entity->setType(strtoupper($type));
        $entity->save();
        return $entity;
    }

    static public function createEntityRelation($idEntity1, $relEntry, $idEntity2, $idEntity3 = null) {
        $rt = new RelationType();
        $rt->getByEntry($relEntry);
        $er = new EntityRelation();
        $er->setIdEntity1($idEntity1);
        $er->setIdEntity2($idEntity2);
        $er->setIdEntity3($idEntity3);
        $er->setIdRelationType($rt->getIdRelationType());
        $er->save();
    }

    static public function updateEntityRelation($idEntity1, $relEntry, $idEntity2) {
        $rt = new RelationType();
        $c = $rt->getCriteria()->select('idRelationType')->where("entry = '{$relEntry}'");
        $er = new EntityRelation();
        $transaction = $er->beginTransaction();
        $criteria = $er->getDeleteCriteria();
        $criteria->where("idEntity1 = {$idEntity1}");
        $criteria->where("idRelationType", "=", $c);
        $criteria->delete();
        $rt->getByEntry($relEntry);
        $er->setIdEntity1($idEntity1);
        $er->setIdEntity2($idEntity2);
        $er->setIdRelationType($rt->getIdRelationType());
        $er->save();
        $transaction->commit();
    }

    static public function deleteEntityRelation($idEntity1, $relEntry, $idEntity2) {
        $rt = new RelationType();
        $c = $rt->getCriteria()->select('idRelationType')->where("entry = '{$relEntry}'");
        $er = new EntityRelation();
        $transaction = $er->beginTransaction();
        $criteria = $er->getDeleteCriteria();
        $criteria->where("idEntity1 = {$idEntity1}");
        $criteria->where("idEntity2 = {$idEntity2}");
        $criteria->where("idRelationType", "=", $c);
        $criteria->delete();
        $transaction->commit();
    }

    static public function deleteEntity1Relation($idEntity, $relEntry) {
        $rt = new RelationType();
        $c = $rt->getCriteria()->select('idRelationType')->where("entry = '{$relEntry}'");
        $er = new EntityRelation();
        $transaction = $er->beginTransaction();
        $criteria = $er->getDeleteCriteria();
        $criteria->where("idEntity1 = {$idEntity}");
        $criteria->where("idRelationType", "=", $c);
        $criteria->delete();
        $transaction->commit();
    }

    static public function deleteEntity2Relation($idEntity, $relEntry) {
        $rt = new RelationType();
        $c = $rt->getCriteria()->select('idRelationType')->where("entry = '{$relEntry}'");
        $er = new EntityRelation();
        $transaction = $er->beginTransaction();
        $criteria = $er->getDeleteCriteria();
        $criteria->where("idEntity2 = {$idEntity}");
        $criteria->where("idRelationType", "=", $c);
        $criteria->delete();
        $transaction->commit();
    }

    static public function deleteAllEntityRelation($idEntity) {
        $er = new EntityRelation();
        $transaction = $er->beginTransaction();
        $er->removeAllFromEntity($idEntity);
        $transaction->commit();
    }

    static public function createConstraintInstance($idConstraint, $constraintTypeEntry, $idConstrained, $idConstrainedBy) {
        $ct = new ConstraintType();
        $ct->getByEntry($constraintTypeEntry);
        $ci = new ConstraintInstance();
        $ci->setIdConstraintType($ct->getId());
        $ci->setIdConstraint($idConstraint);
        $ci->setIdConstrained($idConstrained);
        $ci->setIdConstrainedBy($idConstrainedBy);
        $ci->save();
    }

    static public function getAnnotationStatus($approvement = false, $validation = '1') {
        $level = \Manager::getSession()->fnbrLevel;
        $login = \Manager::getLogin();
        $level = $login->getUser()->getUserLevel();
        if ($level == 'ADMIN') {
            $level = 'MASTER';
        }
        if ($approvement) {
            if ($validation == '1') {
                $entries = [
                    'SENIOR' => 'ast_sr_app',
                    'MASTER' => 'ast_ms_app',
                ];
            } else if ($validation == '0') {
                $entries = [
                    'SENIOR' => 'ast_disapp',
                    'MASTER' => 'ast_disapp',
                ];
            } else if ($validation == '-1') {
                $entries = [
                    'BEGINNER' => 'ast_doubt',
                    'JUNIOR' => 'ast_doubt',
                ];
            } else if ($validation == '-2') {
                $entries = [
                    'BEGINNER' => 'ast_ignore',
                    'JUNIOR' => 'ast_ignore',
                    'SENIOR' => 'ast_ignore',
                    'MASTER' => 'ast_ignore',
                ];
            } else if ($validation == '-3') {
                $entries = [
                    'BEGINNER' => 'ast_rel',
                    'JUNIOR' => 'ast_rel',
                    'SENIOR' => 'ast_rel',
                    'MASTER' => 'ast_rel',
                ];
            }
        } else {
            $entries = [
                'BEGINNER' => 'ast_bg_ann',
                'JUNIOR' => 'ast_jr_ann',
                'SENIOR' => 'ast_sr_ann',
                'ANNO' => 'ast_ms_app',
                'MASTER' => 'ast_ms_app',
                'ADMIN' => 'ast_ms_app',
            ];
        }
        $entry = $entries[$level];
        return $entry;
    }

    static public function userLevel() {
        $group = new Group();
        $list = $group->listByFilter()->asQuery()->chunkResult('name', 'description');
        return $list;
        //return ['READER' => 'Reader', 'BEGINNER' => 'Beginner', 'JUNIOR' => 'Junior', 'SENIOR' => 'Senior', 'MASTER' => 'Master', 'ADMIN' => 'Admin'];
    }

    static public function getCurrentUser() {
        $user = \Manager::getLogin()->getUser();
        return $user;
    }

    static public function getCurrentUserLevel() {
        $userLevel = Base::getCurrentUser()->getUserLevel();
        return $userLevel;
    }
    
    static public function newTimeline($tl, $operation = 'S') {
        $timeline = new Timeline();
        return $timeline->newTimeLine($tl, $operation);
    }

    static public function updateTimeline($oldTl, $newTl) {
        $timeline = new Timeline();
        return $timeline->updateTimeline($oldTl, $newTl);
    }

    static public function entityTimelineSave($idEntity) {
        $entity = new Entity($idEntity);
        $entity->setTimeline(self::newTimeline($entity->getAlias(), 'S'));
        $entity->save();
    }

    static public function entityTimelineDelete($idEntity) {
        $entity = new Entity($idEntity);
        self::newTimeline($entity->getAlias(), 'D');
    }

}
