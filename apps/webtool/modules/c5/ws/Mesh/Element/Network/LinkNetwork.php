<?php

namespace Mesh\Element\Network;

use Mesh\Element\Structure\Link;

/**
 * Class LinkNetwork
 * Infraestrutura de nós e links, herdada pela TypeNetwork e pela RegionNetwork
 */
class LinkNetwork extends Network
{
    public $links;
    public $linksInput;
    public $linksById;
    public $nodesInput;
    public $nodesOutput;

    public function __construct()
    {
        parent::__construct();
        $this->links = [];
        $this->linksInput = [];
        $this->linksById = [];
        $this->nodesInput = [];
        $this->nodesOutput = [];
    }

    public function createLink($nodeSource, $nodeTarget, $params)
    {
        if (($nodeSource == '') || ($nodeTarget == '')) {
            return;
        }
        $idNodeSource = $nodeSource->getId();
        $idNodeTarget = $nodeTarget->getId();
        return $this->createLinkById($idNodeSource, $idNodeTarget, $params);
    }

    public function createLinkById($idNodeSource, $idNodeTarget, $params)
    {
        if ($params->label == 'rel_inhibitory') {
            $params->inhibitory = true;
        }
        $link = new Link($idNodeSource, $idNodeTarget, $params);
        $this->addLink($idNodeSource, $idNodeTarget, $link);
        return $link;
    }

    public function addLink($idSource, $idTarget, $link)
    {
        //$this->dump('addlink source = ' . $idSource . ' - target = ' . $idTarget . ' - idRelayType = ' . $link->idRelayType. ' - head = ' . ($link->head ? ' true' : 'false'));
        $this->linksById[$link->id] = $link;
        $this->links[$idSource][$idTarget] = $link;
        $this->linksInput[$idTarget][$idSource] = $link;
        $this->nodesInput[$idTarget][$idSource] = $idSource;
        $this->nodesOutput[$idSource][$idTarget] = $idTarget;
    }

    public function delLink($idSource, $idTarget)
    {
        $link = $this->links[$idSource][$idTarget];
        $this->linksById[$link->id] = null;
        $this->links[$idSource][$idTarget] = null;
        $this->nodesInput[$idTarget][$idSource] = null;
        $this->nodesOutput[$idSource][$idTarget] = null;
    }

    public function getLink($nodeSource, $nodeTarget)
    {
        return $this->links[$nodeSource->getId()][$nodeTarget->getId()];
    }

    public function getLinkByClass($nodeSource, $nodeTarget)
    {
        return $this->links[$nodeSource->getClass()][$nodeTarget->getId()];
    }

    public function getLinksInput($idTarget, $idSource = '')
    {
        if ($idSource != '') {
            $links = $this->linksInput[$idTarget][$idSource];
        } else {
            $links = $this->linksInput[$idTarget];
        }
        return is_array($links) ? $links : [];
    }

    public function getLinkById($idLink)
    {
        return $this->linksById[$idLink];
    }

}

