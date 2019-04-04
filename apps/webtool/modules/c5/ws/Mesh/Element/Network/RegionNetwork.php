<?php

namespace Mesh\Element\Network;

use Mesh\Element\Node\NodeRegion;
use Mesh\Element\Node\NodeFeature;
use Mesh\Element\Node\NodeRelay;
use Mesh\Element\Structure\Link;

class RegionNetwork extends LinkNetwork
{
    public $tokenNetwork;
    public $typeNetwork;
    public $nodesByLayer;
    public $currentLayer;
    public $currentPhase;
    public $currentWord;
    public $clusterType;
    /*
     * clusters são agrupamentos de nodeRegions
     * este array lista a region ou o type de cada cluster, na ordem em que o cluster deve ser criado
     * se indicar uma region, o cluster é formado por apenas uma nodeRegion, com todos os featuresNodes
     * se indicar um type, o cluster é formado por vários nodeRegions, um para cada featureNode
     */
    public $clusters;
    public $regions;
    public $availableTokens;
    public $projections;
    public $toProcess;
    public $toInhibit;

    public function __construct()
    {
        parent::__construct();
        $this->clusterType = [
            'word' => 'multiple',
            'lexeme' => 'single',
            'lemma' => 'single',
            'lu' => 'single',
            'pos' => 'single',
            'cxn' => 'multiple',
        ];
        $this->regions = [];
        $this->nodesByLayer = [];
        $this->avaliableTokens = [];
        $this->clusters = [
            ['word', 'single'],
            ['lexeme', 'single'],
            ['lemma', 'single'],
            ['lu', 'single'],
            ['pos', 'single'],
        ];
    }

    public function setTokenNetwork(TokenNetwork $tokenNetwork)
    {
        $this->tokenNetwork = $tokenNetwork;
        $this->typeNetwork = $tokenNetwork->typeNetwork;
    }

    public function addNode($node)
    {
        parent::addNode($node);
        $node->manager = $this->manager;
        //$this->nodesByLayer[$node->layer][] = $node->getId();
    }

    public function getRegion($region, $baseRegion = '')
    {
        $nodeRegion = $this->getNodeByName($region);
        if (is_null($nodeRegion)) {
            $nodeRegion = new NodeRegion($this, $baseRegion);
            $this->addNode($nodeRegion);
        }
        return $nodeRegion;
    }

    public function addRegion($nodeRegion)
    {
        $this->regions[$nodeRegion->name] = $nodeRegion;
    }

    public function clearAll()
    {
        foreach ($this->nodes as $node) {
            $id = $node->getId();
            unset($this->nodesByName[$node->getName()]);
            unset($this->nodes[$id]);
            foreach ($this->links as $idSource => $targets) {
                foreach ($targets as $idTarget => $link) {
                    if ($idTarget == $id) {
                        unset($this->links[$idSource][$idTarget]);
                    }
                }
            }
            unset($this->links[$id]);
            unset($this->nodesInput[$id]);
            unset($this->nodesOutput[$id]);
        }
        $this->nodesByName = [];
        $this->nodesByFullName = [];
        $this->nodesByType = [];
        $this->nodesByClass = [];
        $this->nodesByRegion = [];
        $this->linksInput = [];
        $this->linksById = [];
    }

    public function getAvaliableTokens()
    {
        return $this->availableTokens;
    }

    /**
     * Adiciona os tokens que estarão disponível para linkar durante o build
     * Os tokens devem ser Features (not Projection) e status = predicitive
     * @param $tokens
     */
    public function updateAvailableTokens($tokens)
    {
        foreach ($tokens as $token) {
            //$this->availableTokens[] = $token;
            $i = count($this->availableTokens[$token->typeNode->id]);
            $this->availableTokens[$token->typeNode->id][$i] = $token;
            if ($token->typeNode->id != $token->typeNode->class) {
                $i = count($this->availableTokens[$token->typeNode->class]);
                $this->availableTokens[$token->typeNode->class][$i] = $token;
            }
        }
    }

    /**
     * A cada iteração substitui o token relativo à word, na lista de tokens available
     * Ou seja, só uma word pode estar disponível a cada iteração
     * @param $token
     */
    public function setAvailableWordToken($token)
    {
        //$found = false;
        $this->availableTokens['word'][0] = $token;
        /*
        foreach ($this->availableTokens as $i => $avaliableToken) {
            if ($avaliableToken->typeNode->type == 'word') {
                $this->availableTokens[$i] = $token;
                $found = true;
            }
        }
        if (!$found) {
            //$this->availableTokens[] = $token;
            $this->availableTokens['word'] = $token;
        }
        */
    }

    /**
     * Obtém os tokens disponíveis, por type ou class
     * @param $idTypeNode
     * @param int $headWordIndex se informado, o head do token deve ser diferente de headWordIndex
     * @return array
     */
    public function getTokensAvailableByTypeOrClassDep($idTypeNode, $headWordIndex = 0)
    {
        $available = [];
        if (isset($this->availableTokens[$idTypeNode])) {
            $last = count($this->availableTokens[$idTypeNode]) - 1;
            $found = false;
            do {
                $token = $this->availableTokens[$idTypeNode][$last];
                if (($token->h != $headWordIndex) /* && ($token->status == 'predictive')*/)  {
                    $available[] = $token;
                    //$found = true;
                }
                --$last;
            } while (($last >= 0) && (!$found));
        }
        /*

        foreach ($this->availableTokens as $availableToken) {
            if (($availableToken->typeNode->id == $idTypeNode) && ($availableToken->h != $headWordIndex) && ($availableToken->status == 'predictive')) {
                $available[] = $availableToken;
            }
        }
        if (count($available) == 0) {
            foreach ($this->availableTokens as $availableToken) {
                if (($availableToken->typeNode->class == $idTypeNode) && ($availableToken->h != $headWordIndex) && ($availableToken->status == 'predictive')) {
                    $available[] = $availableToken;
                }
            }
        }
        */
        return $available;
    }

    /**
     * Obtém os tokens disponíveis, por type ou class
     * @param $idTypeNode
     * @param int $headWordIndex se informado, o head do token deve ser diferente de headWordIndex
     * @return array
     */
    public function getTokensAvailableByTypeOrClassDiff($idTypeNode, $headWordIndex = 0)
    {
        $available = [];
        if (isset($this->availableTokens[$idTypeNode])) {
            $last = count($this->availableTokens[$idTypeNode]) - 1;
            $found = false;
            do {
                $token = $this->availableTokens[$idTypeNode][$last];
                if (($token->h != $headWordIndex) /* && ($token->status == 'predictive')*/) {
                    $available[] = $token;
                    //$found = true;
                }
                --$last;
            } while (($last >= 0) && (!$found));
        }
        /*
        foreach ($this->availableTokens as $availableToken) {
            if (($availableToken->typeNode->id == $idTypeNode) && ($availableToken->h != $headWordIndex)) {
                $available[] = $availableToken;
            }
        }
        if (count($available) == 0) {
            foreach ($this->availableTokens as $availableToken) {
                if (($availableToken->typeNode->class == $idTypeNode) && ($availableToken->h != $headWordIndex)) {
                    $available[] = $availableToken;
                }
            }
        }
        */
        return $available;
    }

    /**
     * Obtém os tokens disponíveis, por type ou class
     * @param $idTypeNode
     * @param int $headWordIndex se informado, o head do token deve ser igual ao headWordIndex
     * @return array
     */
    public function getTokensAvailableByTypeOrClassHead($idTypeNode, $headWordIndex = 0)
    {
        $available = [];
        if (isset($this->availableTokens[$idTypeNode])) {
            $last = count($this->availableTokens[$idTypeNode]) - 1;
            $found = false;
            do {
                $token = $this->availableTokens[$idTypeNode][$last];
                if (($token->h == $headWordIndex)  /* && ($token->status == 'predictive')*/) {
                    $available[] = $token;
                    //$found = true;
                }
                --$last;
            } while (($last >= 0) && (!$found));
        }

        /*
        foreach ($this->availableTokens as $availableToken) {
            if (($availableToken->typeNode->id == $idTypeNode) && ($availableToken->wordIndex == $headWordIndex)) {
                $available[] = $availableToken;
            }
        }
        if (count($available) == 0) {
            foreach ($this->availableTokens as $availableToken) {
                if (($availableToken->typeNode->class == $idTypeNode) && ($availableToken->wordIndex == $headWordIndex)) {
                    $available[] = $availableToken;
                }
            }
        }
        */
        return $available;
    }

    public function buildRegionWord($wordTypes)
    {
        foreach ($wordTypes as $wordType) {
            $this->currentWord = $wordType->wordIndex;
            $idsOfFeatureNodes = [$wordType->id];
            $this->buildRegion($idsOfFeatureNodes);
        }
    }

    public function build($wordTypeNode)
    {
        $this->layer = 0;
        $this->currentLayer = 0;
        $this->currentWord = $wordTypeNode->wordIndex;
        $this->regions = [];
        $this->projections = [];

        $nodeRegion = $this->getNodeByName($wordTypeNode->name . '_' . $this->currentWord);
        $this->regions[$wordTypeNode->name] = $nodeRegion;

        $this->dump('#####');
        $this->dump('#####');
        $this->dump('#####   build: current word ' . $this->currentWord);
        $this->dump('#####');
        $this->dump('#####');
        $tokens = $this->tokenNetwork->getAvailable($wordTypeNode->getId());
        $wordToken = $tokens[0];
        $wordToken->status = 'predictive';
        $this->setAvailableWordToken($wordToken);
        //$this->buildTokens($tokens);
        $nextTokens = $this->buildToken($wordToken);
        while (count($nextTokens) > 0) {
            $nx = [];
            foreach ($nextTokens as $nextToken) {
                $next = $this->buildToken($nextToken);
                foreach ($next as $n) {
                    $nx[] = $n;
                }
            }
            $nextTokens = [];
            foreach ($nx as $n) {
                $nextTokens[] = $n;
            }
        }
    }

    public function buildToken($tokenNode)
    {
        $nextTokens = [];
        if (($tokenNode->status != 'predictive')) {
            return;
        }
        if ($tokenNode->typeNode->type == 'word') {
            $idsOfFeatureNodes = $this->typeNetwork->getIdNodesOutput($tokenNode->typeNode->id);
        }
        if (count($idsOfFeatureNodes) == 0) {
            $idsOfFeatureNodes = $this->typeNetwork->getFeatureFromValueIdNetwork($tokenNode->typeNode->id);
        }
        if (count($idsOfFeatureNodes) == 0) {
            $idsOfFeatureNodes = $this->typeNetwork->getFeatureFromValueId($tokenNode->typeNode->id);
        }
        if (count($idsOfFeatureNodes) == 0) {
            $idsOfFeatureNodes = $this->typeNetwork->getFeatureFromValueClass($tokenNode->typeNode->class);
        }
        if (count($idsOfFeatureNodes) != 0) {
            // nodes to activate in forwardpass
            $this->toProcess = [$tokenNode];
            // node to inhibits
            $this->toInhibit = [];
            // build by features
            $builtTokens = $this->buildRegion($idsOfFeatureNodes, $tokenNode);
            // active the regions instantiated
            $this->forwardPass();
            // if there are nodes to inhibit do it before forwardPass
            if (count($this->toInhibit) > 0) {
                $this->inhibits();
            }
            // verify the predicitions
            $this->backwardPass();
            // new cycle: get the next tokens
            $this->regions = [];
            $idsOfFeatureNodes = [];
            $nextTokens = $this->getNextTokens($builtTokens);
            // update the list of available tokens
            $this->updateAvailableTokens($nextTokens);
        }
        return $nextTokens;
    }

    /**
     * Constroi as regiões relativas a cada uma das features evocadas
     * Os nomes das regiões recebem como sufixo o número da word atual
     * @param $idsOfTypeNodes lista de features evocadas
     * @param null $tokenNode qual token evocou estas features
     * @return array lista de todos os tokens que foram criados durante o build das regiões
     */
    public function buildRegion($idsOfTypeNodes, $tokenNode = null)
    {
        $tokens = [];
        foreach ($idsOfTypeNodes as $idTypeNode) {
            $this->tokens = [];
            $this->relays = [];

            $featureNode = $this->typeNetwork->getFeatureNode($idTypeNode);
            // cada featureToken criado é colocado em $this->region
            $single = ($this->clusterType[$featureNode->type] == 'single');
            $baseRegion = ($single ? $featureNode->type : $featureNode->name);
            $this->region = $baseRegion . '_' . $this->currentWord;
            //
            $this->createFeatureTokens($featureNode, $tokenNode);
            // só cria ou atualiza a region se houver tokens
            if (count($this->tokens) > 0) {

                $this->dump('##########');
                $this->dump('#####   buildRegion  ' . $this->region . ': idTypeNode ' . $idTypeNode . '   tokenNode ' . $tokenNode->id);
                $this->dump('#####');

                foreach ($this->tokens as $token) {
                    $tokens[] = $token;
                    if (!isset($this->regions[$token->region])) {
                        $nodeRegion = $this->getRegion($token->region, $baseRegion);
                        $this->addRegion($nodeRegion);
                    } else {
                        $nodeRegion = $this->regions[$token->region];
                    }
                    $nodeRegion->addToken($token);
                }
                foreach ($this->regions as $nodeRegion) {
                    $nodeRegion->updateConstraints();
                }
            }
            $this->dump('#####');
            $this->dump('#####   end buildRegion: idTypeNode ' . $idTypeNode);
        }
        return $tokens;

    }

    /**
     * Seleciona os tokens para o próximo round a partir dos tokens gerados no último build
     * Condições:
     *   - Deve ser Feature (not projection)
     *   - status = predictive ou active
     * @param $tokens built tokens
     * @return array
     */
    public function getNextTokens($tokens)
    {
        $predictiveTokens = [];
        foreach ($tokens as $token) {
            //if (($token->wordIndex == $this->currentWord) && ($token instanceof NodeFeature) && ($token->logic != 'N')) { // não é um projection
            if (($token instanceof NodeFeature) && ($token->logic != 'N')) { // não é um projection
                //if (($token->status == 'active') || ($token->status == 'predictive')) {
                if (($token->status == 'predictive')) {
                    $predictiveTokens[] = $token;
                }
            }
        }
        return $predictiveTokens;
    }

    public function createToken($typeNode)
    {
        $tokenNode = $this->tokenNetwork->createNodeToken($typeNode);
        $this->tokens[] = $tokenNode;
        return $tokenNode;
    }

    public function createTokenById($idTypeNode)
    {
        return $this->createToken($this->typeNetwork->getNode($idTypeNode));
    }

    public function createNewToken($params)
    {
        $tokenNode = $this->tokenNetwork->createNewToken($params);
        $this->tokens[] = $tokenNode;
        return $tokenNode;
    }

    public function createTokenProjection($tokenNode)
    {
        $typeNode = $tokenNode->typeNode;
        $projectionNode = $this->tokenNetwork->createNodeToken($typeNode, $tokenNode);
        $this->tokens[] = $projectionNode;
        $projectionNode->logic = 'N';
        return $projectionNode;
    }

    public function createFeatureTokens($featureNode, $demandTokenNode = null)
    {
        if ($featureNode->type == 'word') {
            $featureToken = $this->createToken($featureNode);
            $featureToken->region = $this->region;
            return false;
        }
        $this->dump('##########');
        $this->dump('##### createFeatureTokens for ' . $featureNode->id . (!is_null($demandTokenNode) ? '   demandTokenNode = ' . $demandTokenNode->id : ''));
        $featureTokens = [];
        $idFeatureType = $featureNode->getId();
        $linksInput = $this->typeNetwork->getLinksInput($idFeatureType);
        $structure = 'isa';
        $pools = [];
        $idPoolHead = $idPoolDemand = -1;
        $idPoolDeps = [];
        $idRelayNodes = [];
        foreach ($linksInput as $idPoolType => $link) {
            $pools[] = $idPoolType;
            if ($link->getLabel() == 'rel_elementof') {
                $structure = 'matrix';
            }
            if ($link->head) {
                $idPoolHead = $idPoolType;
            } else {
                $idPoolDeps[] = $idPoolType;
            }
            $idRelayNodes[$idPoolType] = $this->typeNetwork->getIdNodesInput($idPoolType);
            foreach ($idRelayNodes[$idPoolType] as $idRelayNode) {
                $idValueNodes = $this->typeNetwork->getIdNodesInput($idRelayNode);
                foreach ($idValueNodes as $idValueNode) {
                    if (($idValueNode == $demandTokenNode->typeNode->id) || ($idValueNode == $demandTokenNode->typeNode->class)) {
                        $idPoolDemand = $idPoolType;
                    }
                }
            }
        }
        if ((count($pools) > 0) && ($idPoolHead != -1)) {

            if ($structure == 'isa') {
                $featureToken = $this->createToken($featureNode);
                $featureToken->region = $this->region;
                $this->createAndLinkProjection($demandTokenNode, $featureToken);
                $featureToken->h = $demandTokenNode->h;
            }
            if ($structure == 'matrix') {
                // faz a analise por categoria (atom, duo, comp)
                if ($featureNode->category == 'atom') { // só tem um pool head
                    $featureToken = $this->createToken($featureNode);
                    $featureToken->region = $this->region;
                    $poolNodeToken = $this->createTokenById($idPoolHead);
                    $poolNodeToken->region = $featureToken->region;
                    foreach ($idRelayNodes[$idPoolHead] as $idRelayNode) {
                        $relayNodeToken = $this->createTokenById($idRelayNode);
                        $relayNodeToken->region = $featureToken->region;
                        $projectionToken = $this->getProjection($demandTokenNode, $relayNodeToken);
                        $relayNodeToken->createLinkTo($poolNodeToken);
                        $relayNodeToken->idPoolToken = $idPoolHead;
                        $relayNodeToken->idChildToken = $projectionToken->id;
                        $relayNodeToken->idParentToken = $featureToken->id;
                    }
                    $poolNodeToken->createLinkTo($featureToken);
                }
                if ($featureNode->category == 'duo') { // só um pool head e um pool dep
                    if ($idPoolDemand == $idPoolHead) { // se a demanda for head, sempre cria um novo featureToken
                        // verifica se tem pelo menos um token pro pool dep
                        $availableDep = [];
                        foreach ($idPoolDeps as $idPoolDep) {
                            if (count($idRelayNodes[$idPoolDep]) == 0) {
                                continue;
                            }
                            foreach ($idRelayNodes[$idPoolDep] as $idRelayNode) {
                                $idValueNode = array_shift($this->typeNetwork->getIdNodesInput($idRelayNode));
                                $availableDep[$idPoolDep][$idRelayNode] = $this->getTokensAvailableByTypeOrClassDep($idValueNode, $demandTokenNode->wordIndex);
                            }
                        }
                        if (count($availableDep)) {

                            foreach ($availableDep as $idPoolDep => $relays) {
                                foreach ($relays as $idRelayNodeDep => $available) {
                                    foreach ($available as $tokenValueNode) {
                                        // head
                                        $featureToken = $this->createToken($featureNode);
                                        $featureToken->region = $this->region;
                                        $poolNodeToken = $this->createTokenById($idPoolHead);
                                        $poolNodeToken->region = $featureToken->region;
                                        foreach ($idRelayNodes[$idPoolHead] as $idRelayNode) {
                                            $relayNodeToken = $this->createTokenById($idRelayNode);
                                            $relayNodeToken->region = $featureToken->region;
                                            $projectionToken = $this->getProjection($demandTokenNode, $relayNodeToken);
                                            $relayNodeToken->createLinkTo($poolNodeToken);
                                            $relayNodeToken->idPoolToken = $idPoolHead;
                                            $relayNodeToken->idChildToken = $projectionToken->id;
                                            $relayNodeToken->idParentToken = $featureToken->id;
                                        }
                                        $poolNodeToken->createLinkTo($featureToken);

                                        // dep
                                        $poolNodeToken = $this->createTokenById($idPoolDep);
                                        $poolNodeToken->region = $featureToken->region;
                                        $relayNodeToken = $this->createTokenById($idRelayNodeDep);
                                        $relayNodeToken->region = $featureToken->region;
                                        $projectionToken = $this->getProjection($tokenValueNode, $relayNodeToken);
                                        $relayNodeToken->createLinkTo($poolNodeToken);
                                        $relayNodeToken->idPoolToken = $poolNodeToken->id;
                                        $relayNodeToken->idChildToken = $projectionToken->id;
                                        $relayNodeToken->idParentToken = $featureToken->id;
                                        $poolNodeToken->createLinkTo($featureToken);
                                        if ($featureNode->type == 'inhibitory') {
                                            $this->toInhibit[] = [$demandTokenNode, $featureToken];
                                        }

                                    }
                                }
                            }
                        }
                    } else { // a demanda é dep
                        // verifica se tem pelo menos um token pro pool head
                        $availableHead = [];
                        if (count($idRelayNodes[$idPoolHead]) >= 0) {
                            foreach ($idRelayNodes[$idPoolHead] as $idRelayNode) {
                                $idValueNode = array_shift($this->typeNetwork->getIdNodesInput($idRelayNode));
                                $availableHead[$idRelayNode] = $this->getTokensAvailableByTypeOrClassDiff($idValueNode, $demandTokenNode->wordIndex);
                            }
                        }
                        $hasHead = (count($availableHead) > 0);
                        foreach ($availableHead as $available) {
                            $hasHead = $hasHead && (count($available) > 0);
                        }
                        if ($hasHead) {
                            foreach ($availableHead as $idRelayNode => $available) {
                                foreach ($available as $tokenValueNode) {
                                    // head
                                    $featureToken = $this->createToken($featureNode);
                                    $featureToken->region = $this->region;
                                    $poolNodeToken = $this->createTokenById($idPoolHead);
                                    $poolNodeToken->region = $featureToken->region;
                                    //foreach ($idRelayNodes[$idPoolHead] as $idRelayNode) {
                                    $relayNodeToken = $this->createTokenById($idRelayNode);
                                    $relayNodeToken->region = $featureToken->region;
                                    $projectionToken = $this->getProjection($tokenValueNode, $relayNodeToken);
                                    $relayNodeToken->createLinkTo($poolNodeToken);
                                    $relayNodeToken->idPoolToken = $idPoolHead;
                                    $relayNodeToken->idChildToken = $projectionToken->id;
                                    $relayNodeToken->idParentToken = $featureToken->id;
                                    //}
                                    $poolNodeToken->createLinkTo($featureToken);

                                    // dep


                                    $poolNodeToken = $this->createTokenById($idPoolDemand);
                                    $poolNodeToken->region = $featureToken->region;
                                    foreach ($idRelayNodes[$idPoolDemand] as $idRelayNodeDep) {
                                        $relayNodeToken = $this->createTokenById($idRelayNodeDep);
                                        $relayNodeToken->region = $featureToken->region;
                                        $projectionToken = $this->getProjection($demandTokenNode, $relayNodeToken);
                                        $relayNodeToken->createLinkTo($poolNodeToken);
                                        $relayNodeToken->idPoolToken = $idPoolHead;
                                        $relayNodeToken->idChildToken = $projectionToken->id;
                                        $relayNodeToken->idParentToken = $featureToken->id;
                                        if ($featureNode->type == 'inhibitory') {
                                            $this->toInhibit[] = [$demandTokenNode, $featureToken];
                                        }
                                    }
                                    $poolNodeToken->createLinkTo($featureToken);
                                }
                            }
                        }
                    }

                }
                if ($featureNode->category == 'comp') { // só um pool head (obrigatorio) e n pools dep (opcionais)
                    if ($idPoolDemand == $idPoolHead) { // se a demanda for head, sempre cria um novo featureToken
                        $featureToken = $this->createToken($featureNode);
                        $featureToken->region = $this->region;
                        foreach ($idRelayNodes[$idPoolHead] as $idRelayNode) {
                            $poolNodeToken = $this->createTokenById($idPoolHead);
                            $poolNodeToken->region = $featureToken->region;
                            $relayNodeToken = $this->createTokenById($idRelayNode);
                            $relayNodeToken->region = $featureToken->region;
                            $projectionToken = $this->getProjection($demandTokenNode, $relayNodeToken);
                            $relayNodeToken->createLinkTo($poolNodeToken);
                            $relayNodeToken->idPoolToken = $idPoolHead;
                            $relayNodeToken->idChildToken = $projectionToken->id;
                            $relayNodeToken->idParentToken = $featureToken->id;
                            $poolNodeToken->createLinkTo($featureToken);
                        }
                        // deps
                        $availableDep = [];
                        foreach ($idPoolDeps as $idPoolDep) {
                            if (count($idRelayNodes[$idPoolDep]) == 0) {
                                continue;
                            }
                            foreach ($idRelayNodes[$idPoolDep] as $idRelayNode) {
                                $idValueNode = array_shift($this->typeNetwork->getIdNodesInput($idRelayNode));
                                $availableDep[$idPoolDep][$idRelayNode] = $this->getTokensAvailableByTypeOrClassDep($idValueNode);
                            }
                        }
                        //$hasDep = (count($availableDep) > 0);
                        //foreach ($availableDep as $pools) {
                        //    foreach ($pools as $available) {
                        //        $hasDep = $hasDep && (count($available) > 0);
                        //    }
                        //}
                        //if ($hasDep) {

                        foreach ($idPoolDeps as $idPoolType) {
                            $poolNodeToken = $this->createTokenById($idPoolType);
                            $poolNodeToken->region = $featureToken->region;
                            foreach ($availableDep[$idPoolType] as $idRelayNodeDep => $available) {
                                foreach ($available as $tokenValueNode) {
                                    $relayNodeToken = $this->createTokenById($idRelayNodeDep);
                                    $relayNodeToken->region = $featureToken->region;
                                    $projectionToken = $this->getProjection($tokenValueNode, $relayNodeToken);
                                    $relayNodeToken->createLinkTo($poolNodeToken);
                                    $relayNodeToken->idPoolToken = $poolNodeToken->id;
                                    $relayNodeToken->idChildToken = $projectionToken->id;
                                    $relayNodeToken->idParentToken = $featureToken->id;
                                }
                            }
                            $poolNodeToken->createLinkTo($featureToken);
                        }
                        //}

                    } else { // se a demanda for dep, só lika o demandToken nos pools adequados
                        $availableFeatureTokens = $this->getTokensAvailableByTypeOrClassHead($idFeatureType, $demandTokenNode->h);
                        foreach ($availableFeatureTokens as $featureToken) {
                            foreach ($featureToken->inputSites as $site) {
                                $poolNodeToken = $this->tokenNetwork->getNode($site->idSourceToken);
                                if ($poolNodeToken->typeNode->id == $idPoolDemand) {
                                    $idRelayNodes = $this->typeNetwork->getIdNodesInput($idPoolDemand);
                                    foreach ($idRelayNodes as $idRelayNode) {
                                        $idValueNode = array_shift($this->typeNetwork->getIdNodesInput($idRelayNode));
                                        if ($idValueNode == $demandTokenNode->typeNode->id) {
                                            $this->dump('======= ' . $idRelayNode . ' - ' . $poolNodeToken->id . ' - ' . $idPoolDemand);
                                            $relayNodeToken = $this->createTokenById($idRelayNode);
                                            $relayNodeToken->region = $featureToken->region;
                                            $projectionToken = $this->getProjection($demandTokenNode, $relayNodeToken);
                                            $relayNodeToken->createLinkTo($poolNodeToken);
                                            $relayNodeToken->idPoolToken = $poolNodeToken->id;
                                            $relayNodeToken->idChildToken = $projectionToken->id;
                                            $relayNodeToken->idParentToken = $featureToken->id;
                                        }
                                    }
                                }
                                $featureToken->slots->merge($poolNodeToken->getSlots());
                            }
                        }
                    }
                }
                if ($featureNode->category == 'cxn') { // número variável de pools, não tem necessariamente um head
                    // para ser criado, tem de existir todos os values obrigatorios
                    // qualquer elemento obrigatório pode demandar a criação do featureToken

                    $projectionToken = $this->projections[$this->region][$demandTokenNode->id];
                    if (!is_null($projectionToken)) {
                        if ($projectionToken->status == 'constrained') {
                            return;
                        }
                    }

                    $availableElement = [];
                    $x = [];
                    foreach ($pools as $idPoolType) {
                        if (count($idRelayNodes[$idPoolType]) == 0) {
                            continue;
                        }
                        $link = $linksInput[$idPoolType];

                        foreach ($idRelayNodes[$idPoolType] as $idRelayNode) {
                            $idValueNode = array_shift($this->typeNetwork->getIdNodesInput($idRelayNode));
                            if ($idPoolType == $idPoolDemand) {
                                $available = [$demandTokenNode];
                            } else {
                                $available = $this->getTokensAvailableByTypeOrClassDiff($idValueNode);
                            }
                            foreach ($available as $tokenNode) {
                                $x[$idPoolType][$idRelayNode][] = $tokenNode;
                            }
                            $availableElement[$idPoolType][$idRelayNode] = [
                                'available' => $available,
                                'optional' => $link->optional
                            ];
                        }
                    }

                    $hasElement = (count($availableElement) > 0);
                    foreach ($availableElement as $r) {
                        foreach ($r as $a) {
                            if (!$a['optional']) {
                                $hasElement = $hasElement && (count($a['available']) > 0);
                            }
                        }
                    }

                    if ($hasElement) {
                        $k = [];
                        foreach ($x as $idPoolType => $relays) {
                            foreach ($relays as $idRelayNode => $tokens) {
                                $y = [];
                                $i = 0;
                                foreach ($tokens as $token) {
                                    $y[++$i] = [$idPoolType, $idRelayNode, $token];
                                }
                                $n = [];
                                if (count($k) == 0) {
                                    foreach ($y as $i => $trio) {
                                        $n[$i] = [$trio];
                                    }
                                } else {
                                    foreach ($k as $k1) {
                                        $z = [];
                                        foreach ($k1 as $k2) {
                                            $z[] = $k2;

                                        }
                                        foreach ($y as $trio) {
                                            $z[] = $trio;
                                        }
                                        $n[] = $z;
                                    }
                                }
                                $k = $n;
                            }
                        }

                        foreach ($k as $set) {
                            $featureToken = $this->createToken($featureNode);
                            $featureToken->region = $this->region;
                            foreach ($set as $trio) {
                                list($idPoolType, $idRelayNode, $tokenValueNode) = $trio;
                                $poolNodeToken = $this->createTokenById($idPoolType);
                                $poolNodeToken->region = $featureToken->region;
                                $relayNodeToken = $this->createTokenById($idRelayNode);
                                $relayNodeToken->region = $featureToken->region;
                                $projectionToken = $this->getProjection($tokenValueNode, $relayNodeToken);
                                $relayNodeToken->createLinkTo($poolNodeToken);
                                $relayNodeToken->idPoolToken = $idPoolHead;
                                $relayNodeToken->idChildToken = $projectionToken->id;
                                $relayNodeToken->idParentToken = $featureToken->id;
                                $poolNodeToken->createLinkTo($featureToken);
                            }
                        }
                    }

                }
            }

        }
    }

    public function getProjection($childFeatureToken, $relayNodeToken)
    {
        $projectionToken = $this->projections[$this->region][$childFeatureToken->id];
        if (is_null($projectionToken)) {
            $projectionToken = $this->createAndLinkProjection($childFeatureToken, $relayNodeToken);
        } else {
            $this->linkProjection($projectionToken, $relayNodeToken);
        }
        $this->toProcess[$childFeatureToken->id] = $childFeatureToken;
        return $projectionToken;
    }

    public function linkProjection($projection, $featureToken)
    {
        $projection->createLinkTo($featureToken);
    }


    public function createAndLinkProjection($childFeatureToken, $featureToken)
    {
        $childFeatureType = $childFeatureToken->typeNode;
        $projection = $this->createTokenProjection($childFeatureToken);
        $projection->region = $featureToken->region;
        $this->dump('create/get Projection For Isa componentToken = ' . $projection->getId());
        //$link = new Link($childFeatureType->typeNode->id, $childFeatureType->typeNode->id, (object)['label' => 'rel_projection', 'head' => '1']);
        $link = new Link($childFeatureType->id, $childFeatureType->id, (object)['label' => 'rel_projection', 'head' => '1']);
        $this->tokenNetwork->createSite($childFeatureToken, $projection, $link);
        $childFeatureToken->createLinkTo($projection);
        if ($childFeatureToken->slots->getValue() != $projection->slots->getValue()) {
            $x = 0;
        }
        $this->projections[$this->region][$childFeatureToken->id] = $projection;
        $projection->createLinkTo($featureToken);
        //$childFeatureToken->process();
        return $projection;
    }

    /*
     * Activation
     */

    public function forwardPass()
    {
        $this->dump('##########');
        $this->dump('#####   activate');
        $this->dump('#####');
        foreach ($this->regions as $nodeRegion) {
            $this->dump('#####     region ' . $nodeRegion->name);
        };
        $this->currentLayer = 1;
        $this->currentPhase = 'feature';
        $end = $this->process(); // end == true, if there are no regions in currentLayer
        return $end;
    }

    public function process()
    {
        $this->regions = [];
        $next = [];
        foreach ($this->toProcess as $tokenNode) {
            $nextProjections = $tokenNode->process();
            foreach ($nextProjections as $projectionNode) {
                $next[] = $projectionNode;
            }
        }
        do {
            $regions = [];
            foreach ($next as $nextNode) {
                $regions[$nextNode->region] = $nextNode->region;
            }
            $next = [];
            if (count($regions) > 0) {
//                $regions = $this->regions;
                foreach ($regions as $regionName) {
                    $regionNode = $this->getRegion($regionName);
                    $this->regions[] = $regionNode;
                    $this->dump('##########');
                    $this->dump('#####   process: region = ' . $regionNode->region);
                    $this->dump('#####');
                    $regionNode->countTokens();
                    $regionNode->processRelay();
                    $regionNode->processPool();
                    $nextProjections = $regionNode->processFeature();
                    foreach ($nextProjections as $projectionNode) {
                        $next[] = $projectionNode;
                    }
                    $this->dump('#####');
                    $this->dump('#####   end process: region = ' . $regionNode->region);
                }
            }
        } while (count($next) > 0);

    }

    public function backwardPass()
    {
        $regions = $this->regions;
        $relays = [];
        $projections = [];
        foreach ($regions as $regionNode) {
            $this->dump('##########');
            $this->dump('#####   backwardPass: region = ' . $regionNode->region);
            $this->dump('#####');
            $regionNode->backwardAddRelays($relays);
            $regionNode->backwardAddProjections($projections);
            $this->dump('#####');
            $this->dump('#####   end backwardPass: region = ' . $regionNode->region);
        }
        foreach ($relays as $idProjectionToken => $relayTokens) {
            $n = count($relayTokens);
            $m = 0;
            foreach ($relayTokens as $relayToken) {
                if ($relayToken->status != 'active') {
                    ++$m;
                }
            }
            if ($n == $m) {
                $projectionToken = $this->tokenNetwork->getNode($idProjectionToken);
                $projectionToken->status = 'constrained';
            }
        }

        foreach ($projections as $idFeatureToken => $projectionTokens) {
            $n = count($projectionTokens);
            $m = 0;
            foreach ($projectionTokens as $projectionToken) {
                if ($projectionToken->status != 'active') {
                    ++$m;
                }
            }
            if ($n == $m) {
                $idFeatureToken = $this->tokenNetwork->getNode($idFeatureToken);
                //if ($idFeatureToken->status == 'active') {
                    $idFeatureToken->status = 'predictive';
                //}
            }
        }
    }

    public function inhibits()
    {
        foreach ($this->toInhibit as $toInhibit) {
            $featureTokenNode = $toInhibit[1];
            if (($featureTokenNode->status == 'active') || ($featureTokenNode->status == 'predictive')) {
                /*
                $featureTokenNode->status = 'inhibited';
                foreach ($featureTokenNode->inputSites as $sitePool) {
                    if (!$sitePool->head()) {
                        $poolNodeToken = $this->tokenNetwork->getNode($sitePool->idSourceToken);
                        $poolNodeToken->status = 'inhibited';
                        foreach ($poolNodeToken->inputSites as $siteRelay) {
                            $relayNodeToken = $this->tokenNetwork->getNode($siteRelay->idSourceToken);
                            $relayNodeToken->status = 'inhibited';
                            foreach ($relayNodeToken->inputSites as $siteProjection) {
                                $projectionNodeToken = $this->tokenNetwork->getNode($siteProjection->idSourceToken);
                                $projectionNodeToken->status = 'inhibited';
                            }
                        }
                    }
                }
                */
                $childTokenNode = $toInhibit[0];
                $childTokenNode->status = 'inhibited';
                foreach ($childTokenNode->outputSites as $siteProjection) {
                    $projectionNodeToken = $this->tokenNetwork->getNode($siteProjection->idTargetToken);
                    $projectionNodeToken->status = 'inhibited';
                    foreach ($projectionNodeToken->outputSites as $siteRelay) {
                        $relayNodeToken = $this->tokenNetwork->getNode($siteRelay->idTargetToken);
                        $relayNodeToken->status = 'inhibited';
                        foreach ($relayNodeToken->outputSites as $sitePool) {
                            $poolNodeToken = $this->tokenNetwork->getNode($sitePool->idTargetToken);
                            $poolNodeToken->status = 'inhibited';
                            foreach ($poolNodeToken->outputSites as $siteFeature) {
                                $featureNodeToken = $this->tokenNetwork->getNode($siteFeature->idTargetToken);
                                $featureNodeToken->status = 'inhibited';
                            }
                        }
                    }
                }

            }
        }
    }


}
