<?php
namespace Mesh\Element\Structure;

use Mesh\Infra\Base;

/*
 * lista de Sites de uma TokenNetwork
 * Vários métodos de acesso
 */
class SiteList extends Base
{
    public $inputSites;
    public $outputSites;
    public $sites;

    function __construct()
    {
        $this->inputSites = [];
        $this->outputSites = [];
        $this->sites = [];
    }

    public function initialize($idTokenNode) {
        $this->inputSites[$idTokenNode] = [];
        $this->outputSites[$idTokenNode] = [];
        $this->sites[$idTokenNode] = [];
    }

    public function createSite($sourceNodeToken, $targetNodeToken, $link) {
        $site = new Site($sourceNodeToken->id, $targetNodeToken->id, $link);
        $this->outputSites[$sourceNodeToken->id][$targetNodeToken->id] = $site;
        $this->inputSites[$targetNodeToken->id][$sourceNodeToken->id] = $site;
        $this->sites[$sourceNodeToken->id][] = $site;
        $this->sites[$targetNodeToken->id][] = $site;
    }

    public function getOutputSites($idTokenNode) {
        return $this->outputSites[$idTokenNode];
    }

    public function getInputSites($idTokenNode) {
        return $this->inputSites[$idTokenNode];
    }

    public function getAllSites($idTokenNode) {
        return $this->sites[$idTokenNode];
    }

    public function getOutputSiteFromTo($sourceNodeToken, $targetNodeToken, $create = true) {
        $site =  $this->outputSites[$sourceNodeToken->id][$targetNodeToken->id];
        if (($site == '') && $create) {
            $link = $sourceNodeToken->tokenNetwork->getLinkFor($sourceNodeToken, $targetNodeToken);
            $this->createSite($sourceNodeToken, $targetNodeToken, $link);
            $site = $this->outputSites[$sourceNodeToken->id][$targetNodeToken->id];
        }
        return $site;

    }

    public function getInputSiteFromTo($targetNodeToken, $sourceNodeToken, $create = true) {
        $site =  $this->inputSites[$targetNodeToken->id][$sourceNodeToken->id];
        if (($site == '') && $create) {
            $link = $sourceNodeToken->tokenNetwork->getLinkFor($sourceNodeToken, $targetNodeToken);
            $this->createSite($sourceNodeToken, $targetNodeToken, $link);
            $site =  $this->inputSites[$targetNodeToken->id][$sourceNodeToken->id];
        }
        return $site;

    }

}

