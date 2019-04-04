<?php

/* Copyright [2011, 2013, 2017] da Universidade Federal de Juiz de Fora
 * Este arquivo é parte do programa Framework Maestro.
 * O Framework Maestro é um software livre; você pode redistribuí-lo e/ou
 * modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada
 * pela Fundação do Software Livre (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil,
 * mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer
 * MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL
 * em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título
 * "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software
 * Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a
 * Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

/**
 * MRenderBinary.
 * Retorna dados para download de arquivo. Tem tratamento específico na classe MResponse.
 */
class MRenderBinary extends MResult
{

    protected $stream;
    protected $inline;
    protected $fileName;
    protected $filePath;

    public function __construct($stream, $inline = true, $fileName = '', $filePath = '')
    {
        parent::__construct();
        $this->stream = $stream;
        $this->inline = $inline;
        $this->fileName = $fileName ?: basename($filePath);
        $this->filePath = $filePath;
    }

    public function getStream()
    {
        return $this->stream;
    }

    public function getInline()
    {
        return $this->inline;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function apply($request, $response)
    {

    }

}

