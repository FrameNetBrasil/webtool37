<?php
/**
 * Created by PhpStorm.
 * User: ematos
 * Date: 19/07/2018
 * Time: 11:17
 */

namespace C5\ORM\Service;

class BaseService
{
    protected $idLanguage;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager) {
        $this->manager = $entityManager;
    }

    public function setIdLanguage($idLanguage) {
        $this->idLanguage = $idLanguage;
    }

    public function query(string $sql) {
        $conn = $this->manager->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function execute(string $sql) {
        $conn = $this->manager->getConnection();
        //$conn->execute("SET SESSION interactive_timeout = 28800");
        //$conn->execute("SET SESSION wait_timeout = 28800");
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $conn->lastInsertId();
    }
}