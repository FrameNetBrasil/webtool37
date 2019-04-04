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
 * Brief Class Description.
 * Complete Class Description.
 */
class MDataTransformer
{

    static public function data($data)
    {
        if ($data instanceof \StdClass) {
            return $data;
        }
        if (is_array($data)) {
            $aux = [];
            foreach ($data as $i => $value) {
                $aux[$i] = self::data($value);
            }
            return $aux;
        }
        if ($data instanceof \MModel) {
            return $data->getData();
        }
        if ($data instanceof \Association) {
            return $data->getObjects();
        }
        return (object)[
            'data' => $data
        ];
    }

    static public function json($data)
    {
        if (is_array($data)) {
            return json_encode($data);
        }
        if ($data instanceof \StdClass) {
            return json_encode($data);
        }
        if ($data instanceof \MModel) {
            return json_encode(self::data($data));
        }
        if ($data instanceof \Association) {
            return json_encode(self::data($data));
        }
        return json_encode((object)[
            'data' => $data
        ]);
    }
}
