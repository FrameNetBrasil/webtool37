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
 * DataType com as possíveis situações de uma assinatura.
 *
 * VALIDA: Verificada com o certificado atual do usuario.
 *
 * CERT_EXPIRADO: Não pode ser verificada com o certificado atual.
 *  O certificado extraido da assinatura tem o mesmo CPF do usuário que assinou mas está expirado.
 *  Provavelmente porque o usuário atualizou o seu certificado após sua expiração.
 *
 * CERT_ANTIGO: Não pode ser verificada com o certificado atual.
 *  O certificado extraido da assinatura tem o mesmo CPF do usuário que assinou mas ainda não expirou.
 *  Provavelmente o usuário alterou seu certificado por algum motivo de força maior.
 *
 * INVALIDA: Não pode ser verificada com o certificado atual e o CPF extraído é diferente do usuário que assinou.
 *  Essa situação não deveria ocorrer em cenários mais corriqueiros já que para salvar o certificado é feita a
 *  verificação do CPF do usuário. Pode vir a ocorrer em situações onde o CPF foi alterado posteriormente no sistema
 *  por algum motivo.
 *
 */
class MStatusAssinatura extends MEnumBase
{
    const VALIDA = 0;
    const CERT_EXPIRADO = 1;
    const CERT_ANTIGO = 2;
    const INVALIDA = 3;

    public function getDefault()
    {
        return self::VALIDA;
    }
}

