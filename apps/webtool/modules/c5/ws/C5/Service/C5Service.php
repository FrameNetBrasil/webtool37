<?php

namespace C5\Service;

use C5\Infra\Manager;

class C5Service
{
    private $container;
    private $cxn;
    private $idCxn;
    private $idConcepts;
    private $fullNetwork;
    private $typeNetwork;
    private $tokenNetwork;
    private $regionNetwork;

    private $wordNodes;
    private $currentPhase;
    private $currentWord;
    private $lastWord;
    public $resourceGrammar;
    public $resourceFN;
    public $resourceLex;
    public $resourceUD;
    public $resourceFL;
    public $resourceFull;
    public $manager;
    public $wfUD;
    public $wfStructure;
    public $words;

    public $tetris;
    public $currentTetris;

    public $grammarPath;
    public $dataPath;
    public $udService;

    public function __construct()
    {
        $this->container = require __DIR__ . '/../DI/bootstrap.php';
        $this->initialized = false;
        $this->manager = $this->container->get(Manager::class);
        $this->resourceFull = $this->container->get('ResourceFull');
        $this->words = [];
        $this->currentTetris = 0;
        $this->idConcepts = [];
    }

    public function setIdConcepts($idConcepts) {
        $this->idConcepts = $idConcepts;
    }

    public function generateFullNetwork()
    {
        $this->manager->dump('*************************************************************');
        $this->manager->dump("'***** Generate Full Network");
        $this->manager->dump('*************************************************************');
        $this->resourceFull->clean();
        $this->manager->dump('count nodes = ' . $this->resourceFull->countNodes());
        $this->manager->dump('**** loadFull');
        $this->resourceFull->loadFull($this->idConcepts);
        $this->manager->dump('count nodes = ' . $this->resourceFull->countNodes());
        $this->manager->dump('*************************************************************');
        $this->manager->dump("'***** End Generate Full Network");
        $this->manager->dump('*************************************************************');
        return $this->resourceFull->countNodes();
    }

    public function loadFullNetwork()
    {
        $this->generateFullNetwork();
        $this->fullNetwork = $this->manager->createFullNetwork();
        $this->fullNetwork->dump('[CLEAR]');
        $this->resourceFull->setFullNetwork($this->fullNetwork);
        $this->resourceFull->loadFullNetwork();
        $data = $this->fullNetwork->getStructureGraphViz();
        return $data;

    }


    public function resetNetwork()
    {
        $this->manager->logMessage("* reseting ");
        return '';
    }

    public function showTypeNetwork($sentence = '')
    {
        //$this->typeNetwork = $this->manager->createTypeNetwork();
        $this->sentence = $sentence;
        $this->manager->dump($this->sentence);
        $this->sentenceTypeNetwork($sentence);
        $data = $this->typeNetwork->getStructureGraphViz();
        return $data;
    }

    public function buildTokenNetwork($cxn = '')
    {
        $cxn = trim($cxn);
        if ($cxn != '') {
            try {
                //temp
                $this->loadFullNetwork();
                //
                $this->currentPhase = 1;
                $this->cxn = $cxn;
                $this->manager->dump($this->cxn);
                $this->conceptNetwork = $this->manager->createConceptNetwork();
                $this->conceptNetwork->setTypeNetwork($this->fullNetwork);
                $this->conceptNetwork->build($cxn);
                $this->manager->setLogLevel(2);
                $data = $this->conceptNetwork->getStructureGraphViz();
                //print_r($data);
                return $data;
            } catch (\Exception $e) {
                print_r($e->getMessage());
                print_r($e->getTrace());
                return '';
            }
        } else {
            return '';
        }
        //print_r($data);
        //return '';
    }

    public function initInteration($sentence)
    {
        try {
            $this->currentWord = 0;
            $this->sentence = $sentence;
            $this->manager->dump($this->sentence);
            $this->sentenceTypeNetwork();
            $this->tokenNetwork = $this->manager->createTokenNetwork();
            $this->regionNetwork = $this->manager->createRegionNetwork();
            $this->regionNetwork->buildRegionWord($this->wordNodes);
            $this->manager->setLogLevel(2);
            $data = $this->tokenNetwork->getStructureGraphViz();
            return $data;
        } catch (\Exception $e) {
            print_r($e->getMessage());
            print_r($e->getTrace());
            return '';
        }
    }

    public function nextInteration($sentence)
    {
        $this->manager->logMessage('[CLEAR]');
        //$end = $this->regionNetwork->activateNext();
        //$end = $this->rhNetwork->activateNext();
        $i = ++$this->currentWord;
        if ($i <= $this->lastWord) {
            $wordTypeNode = $this->wordNodes[$i];
            $this->manager->logMessage("* nextInteration - currentWord = " . $this->currentWord);
            $this->regionNetwork->build($wordTypeNode);
            $this->manager->setLogLevel(2);
            $data = $this->tokenNetwork->getStructureGraphViz();
        } else {
            $data = '';
        }
        /*
                if ($end) {
                    $data = '';
                } else {
                    $data = $this->tokenNetwork->getUpdatedGraph();
                }
        */
        return $data;
    }

    public function fullActivation($idCxn)
    {
        //$cxn = trim($cxn);
        if ($idCxn != '') {
            try {
                //temp
                $this->loadFullNetwork();
                //

                $this->manager->setLogLevel(2);
                $this->currentPhase = 1;
                $this->idCxn = $idCxn;
                $this->manager->dump($this->cxn);
                $this->conceptNetwork = $this->manager->createConceptNetwork();
                $this->conceptNetwork->setTypeNetwork($this->fullNetwork);
                $this->conceptNetwork->build($idCxn);

                //$data = $this->conceptNetwork->getStructureGraphViz();
                //return $data;

                $end = $this->conceptNetwork->activate($idCxn);
                while (!$end) {
                    $end = $this->conceptNetwork->activateNext();
                    if ($end) {
                        $data = $this->conceptNetwork->getStructureGraphViz();
                    }
                }


                //$this->manager->dump($data);

                return $data;
            } catch (\Exception $e) {
                print_r($e->getMessage());
                print_r($e->getTrace());
                return '';
            }
        } else {
            return '';
        }
    }

    public function fullActivationQuery($idCxn)
    {
        if ($idCxn != '') {
            try {
                $this->loadFullNetwork();
                $this->manager->setLogLevel(2);
                $this->currentPhase = 1;
                $this->idCxn = $idCxn;
                $this->manager->dump($this->cxn);
                $this->conceptNetwork = $this->manager->createConceptNetwork();
                $this->conceptNetwork->setTypeNetwork($this->fullNetwork);
                $this->conceptNetwork->build($idCxn);
                $end = $this->conceptNetwork->activate($idCxn);
                while (!$end) {
                    $end = $this->conceptNetwork->activateNext();
                    if ($end) {
                        $data = $this->conceptNetwork->getCxnNodes();
                    }
                }
                return $data;
            } catch (\Exception $e) {
                print_r($e->getMessage());
                print_r($e->getTrace());
                return '';
            }
        } else {
            return '';
        }
    }


}