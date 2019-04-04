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

class MPhonetic
{
    public function removeAccentuation($word)
    {
        $acento['Ã'] = $acento['Ã'] = $acento['Ã'] = $acento['Ã'] = 'A';
        $acento['Ã'] = $acento['Ã'] = $acento['Ã'] = 'E';
        $acento['Ã'] = $acento['Ã'] = $acento['Ã'] = $acento['Ã'] = 'O';
        $acento['Ã'] = $acento['Ã'] = $acento['Ã'] = 'I';
        $acento['Ã'] = $acento['Ã'] = $acento['Ã'] = 'U';
        $acento['Ã'] = 'N';
        $acento['Ã'] = 'SS';
        $len = strlen($word);
        $result = '';

        for ($i = 0; $i < $len; $i++) { // percorre toda a string
            $letra = $word[$i];

            if (($l = $acento[$letra]) != NULL)
                $result .= $l;
            else
                $result .= $letra;
        }

        return $result;
    }

    public function removeMultiple($word)
    {
        $len = strlen($word);
        $result = $word[0];

        for ($i = 1; $i < $len; $i++) { // percorre toda a string
            $letra = $word[$i];

            if ($letra != $word[$i - 1])
                $result .= $letra;
        }

        return $result;
    }

    public function removeStrange($word)
    {
        $len = strlen($word);
        $result = '';

        for ($i = 0; $i < $len; $i++) { // percorre toda a string
            $letra = $word[$i];

            if (($letra != "'") && ($letra != "\"") && ($letra != "-") && ($letra != ".") && ($letra != ",")
                && ($letra != "/") && ($letra != "Âª") && ($letra != "Âº")
            )
                $result .= $letra;
        }

        return $result;
    }

    public function fonetize($word)
    {
        $fono['_'] = 'Y';
        $fono['E'] = 'i';
        $fono['&'] = 'i';
        $fono['I'] = 'i';
        $fono['Y'] = 'i';
        $fono['O'] = 'o';
        $fono['U'] = 'o';
        $fono['A'] = 'a';
        $fono['A'] = 'a';
        $fono['S'] = 's';

        $word = trim(strtoupper($word)); // padroniza todas as letras em maiusculo
        $word = $this->removeStrange($word);
        $word = $this->removeAccentuation($word);
        $len = strlen($word);
        $fonaux = '';

        for ($i = 0; $i < $len; $i++) { // percorre toda a string
            $letra = $word[$i];

            if (($l = $fono[$letra]) != NULL)
                $fonaux .= $l;
            else
                $fonaux .= $letra;
        }

        if ($len == 3) {
            if (($fonaux{0} == 'a') || ($fonaux{0} == 'i') || ($fonaux{0} == 'o')) {
                if (($fonaux{1} == 'a') || ($fonaux{1} == 'i') || ($fonaux{1} == 'o')) {
                    if (($fonaux{2} == 'a') || ($fonaux{2} == 'i') || ($fonaux{2} == 'o'))
                        return $fonaux;
                }
            }
        }

        $j = 0;
        $fonaux .= ' ';

        for ($i = 0; $i < $len; $i++) { // percorre toda a string
            $letra = $fonaux[$i];
            $copfon = 0;
            $copmud = 0;
            $newmud = 0;

            switch ($letra) {
                case 'a':
                    if (($fonaux[$i + 1] == 's') || ($fonaux[$i + 1] == 'Z') || ($fonaux[$i + 1] == 'M')
                        || ($fonaux[$i + 1] == 'N')
                    )
                        if ($fonaux[$i + 2] != ' ')
                            $copfon = 1;
                        else {
                            $fonwrk[$j] = 'a';
                            $fonwrk[$j + 1] = ' ';
                            $j++;
                            $i++;
                        }
                    else
                        $copfon = 1;

                    break;

                case 'B':
                    $copmud = 1;

                    break;

                case 'C':
                    $x = 0;

                    if ($fonaux[$i + 1] == 'i') {
                        $fonwrk[$j] = 's';
                        $j++;
                        break;
                    }

                    if (($fonaux[$i + 1] == 'o') && ($fonaux[$i + 2] == 'i') && ($fonaux[$i + 3] == 's')
                        && ($fonaux[$i + 4] == ' ')
                    ) {
                        $fonwrk[$j] = 'K';
                        $fonwrk[$j + 1] = 'a';
                        $fonwrk[$j + 2] = 'o';
                        $i += 4;
                        break;
                    }

                    if ($fonaux[$i + 1] == 'T')
                        break;

                    if ($fonaux[$i + 1] != 'H') {
                        $fonwrk[$j] = 'K';
                        $newmud = 1;

                        if ($fonaux[$i + 1] != 'K') {
                            $i++;
                            break;
                        } else
                            break;
                    }

                    if ($fonaux[$i + 1] == 'H') {
                        if ($fonaux[$i + 2] == 'i')
                            if (($fonaux[$i + 3] == 'a') || ($fonaux[$i + 3] == 'i') || ($fonaux[$i + 3] == 'o'))
                                $x = 1;
                            else
                                ;
                        else
                            ;
                    } else {
                        if ($fonaux[$i + 3] == 'T')
                            if ($fonaux[$i + 4] == 'i')
                                if ($fonaux[$i + 5] == ' ')
                                    $x = 1;
                    }

                    if ($x == 1) {
                        $fonwrk[$j] = 'K';
                        $j++;
                        $i++;
                        break;
                    }

                    if ($j > 0)
                        if ($fonwrk[$j - 1] == 's')
                            $j--;

                    $fonwrk[$j] = 'X';
                    $newmud = 1;
                    $i++;
                    break;

                case 'D': //se o caracter for D
                    $x = 0;

                    //procura por dor
                    if ($fonaux[$i + 1] != 'o') {
                        $copmud = 1;
                        break;
                    } //if

                    else if ($fonaux[$i + 2] == 'R')
                        if ($i != 0)
                            $x = 1;      // dor nao inicial
                        else
                            $copfon = 1; // dor inicial

                    else
                        $copfon = 1;     // nao e dor

                    if ($x == 1)
                        if ($fonaux[$i + 3] == 'i')
                            if ($fonaux[$i + 4] == 's') // dores
                                if ($fonaux[$i + 5] != ' ')
                                    $x = 0;             // nao e dores
                                else
                                    ;
                            else
                                $x = 0;

                        else if ($fonaux[$i + 3] == 'a')
                            if ($fonaux[$i + 4] != ' ')
                                if ($fonaux[$i + 4] != 's')
                                    $x = 0;

                                else if ($fonaux[$i + 5] != ' ')
                                    $x = 0;

                                else
                                    ;
                            else
                                ;

                        else
                            $x = 0;
                    else
                        $x = 0;

                    if ($x == 1) {
                        $fonwrk[$j] = 'D';
                        $fonwrk[$j + 1] = 'o';
                        $fonwrk[$j + 2] = 'R';
                        $i = $i + 5;
                    } //if
                    else
                        $copfon = 1;

                    break;

                case 'F': //se o caracter for F

                    //F nao eh modificado
                    $copmud = 1;

                    break;

                case 'G': //se o caracter for G

                    //gui -> gi
                    if ($fonaux[$i + 1] == 'o')
                        if ($fonaux[$i + 2] == 'i') {
                            $fonwrk[$j] = 'G';
                            $fonwrk[$j + 1] = 'i';
                            $j += 2;
                            $i += 2;
                        } //if
                        //diferente de gui copia como consoante muda
                        else
                            $copmud = 1;

                    else

                        //gl
                        if ($fonaux[$i + 1] == 'L')
                            if ($fonaux[$i + 2] == 'i')

                                //gli + vogal -> li + vogal
                                if (($fonaux[$i + 3] == 'a') || ($fonaux[$i + 3] == 'i') || ($fonaux[$i + 3] == 'o')) {
                                    $fonwrk[$j] = $fonaux[$i + 1];
                                    $fonwrk[$j + 1] = $fonaux[$i + 2];
                                    $j += 2;
                                    $i += 2;
                                } //if
                                else

                                    //glin -> lin
                                    if ($fonaux[$i + 3] == 'N') {
                                        $fonwrk[$j] = $fonaux[$i + 1];
                                        $fonwrk[$j + 1] = $fonaux[$i + 2];
                                        $j += 2;
                                        $i += 2;
                                    } /*if*/
                                    else
                                        $copmud = 1;
                            else
                                $copmud = 1;

                        else

                            //gn + vogal -> ni + vogal
                            if ($fonaux[$i + 1] == 'N')
                                if (($fonaux[$i + 2] != 'a') && ($fonaux[$i + 2] != 'i') && ($fonaux[$i + 2] != 'o'))
                                    $copmud = 1;
                                else {
                                    $fonwrk[$j] = 'N';
                                    $fonwrk[$j + 1] = 'i';
                                    $j += 2;
                                    $i++;
                                } //else

                            else

                                //   ghi -> gi
                                if ($fonaux[$i + 1] == 'H')
                                    if ($fonaux[$i + 2] == 'i') {
                                        $fonwrk[$j] = 'G';
                                        $fonwrk[$j + 1] = 'i';
                                        $j += 2;
                                        $i += 2;
                                    } //if
                                    else
                                        $copmud = 1;

                                else
                                    $copmud = 1;

                    break;

                case 'H': //se o caracter for H

                    //H eh desconsiderado
                    break;

                case 'i': //se o caracter for i
                    if ($fonaux[$i + 2] == ' ')

                        //is ou iz final perde a consoante
                        if ($fonaux[$i + 1] == 's') {
                            $fonwrk[$j] = 'i';
                            break;
                        } //if
                        else if ($fonaux[$i + 1] == 'Z') {
                            $fonwrk[$j] = 'i';
                            break;
                        } //if

                    //ix
                    if ($fonaux[$i + 1] != 'X')
                        $copfon = 1;

                    else if ($i != 0)
                        $copfon = 1;
                    else

                        //ix vogal no inicio torna-se iz
                        if (($fonaux[$i + 2] == 'a') || ($fonaux[$i + 2] == 'i') || ($fonaux[$i + 2] == 'o')) {
                            $fonwrk[$j] = 'i';
                            $fonwrk[$j + 1] = 'Z';
                            $j += 2;
                            $i++;
                            break;
                        } //if
                        else

                            //ix consoante no inicio torna-se is
                            if ($fonaux[$i + 2] == 'C' || $fonaux[$i + 2] == 's') {
                                $fonwrk[$j] = 'i';
                                $j++;
                                $i++;
                                break;
                            } //if
                            else {
                                $fonwrk[$j] = 'i';
                                $fonwrk[$j + 1] = 's';
                                $j += 2;
                                $i++;
                                break;
                            }     //else

                    break;

                case 'J': //se o caracter for J

                    //J -> Gi
                    $fonwrk[$j] = 'G';

                    $fonwrk[$j + 1] = 'i';
                    $j += 2;
                    break;

                case 'K': //se o caracter for K
                    //KT -> T
                    if ($fonaux[$i + 1] != 'T')
                        $copmud = 1;

                    break;

                case 'L': //se o caracter for L

                    //L + vogal nao eh modificado
                    if (($fonaux[$i + 1] == 'a') || ($fonaux[$i + 1] == 'i') || ($fonaux[$i + 1] == 'o'))
                        $copfon = 1;
                    else

                        //L + consoante -> U + consoante
                        if ($fonaux[$i + 1] != 'H') {
                            $fonwrk[$j] = 'o';
                            $j++;
                            break;
                        } //if

                        //LH + consoante nao eh modificado
                        else if ($fonaux[$i + 2] != 'a' && $fonaux[$i + 2] != 'i' && $fonaux[$i + 2] != 'o')
                            $copfon = 1;

                        else //LH + vogal -> LI + vogal
                        {
                            $fonwrk[$j] = 'L';
                            $fonwrk[$j + 1] = 'i';
                            $j += 2;
                            $i++;
                            break;
                        }

                    break;

                case 'M': //se o caracter for M

                    //M + consoante -> N + consoante
                    //M final -> N
                    if (($fonaux[$i + 1] != 'a' && $fonaux[$i + 1] != 'i' && $fonaux[$i + 1] != 'o')
                        || ($fonaux[$i + 1] == ' ')
                    ) {
                        $fonwrk[$j] = 'N';
                        $j++;
                    } //if

                    //M nao eh alterado
                    else
                        $copfon = 1;

                    break;

                case 'N': //se o caracter for N

                    //NGT -> NT
                    if (($fonaux[$i + 1] == 'G') && ($fonaux[$i + 2] == 'T')) {
                        $fonaux[$i + 1] = 'N';
                        $copfon = 1;
                    } //if

                    else

                        //NH + consoante nao eh modificado
                        if ($fonaux[$i + 1] == 'H')
                            if (($fonaux[$i + 2] != 'a') && ($fonaux[$i + 2] != 'i') && ($fonaux[$i + 2] != 'o'))
                                $copfon = 1;

                            //NH + vogal -> Ni + vogal
                            else {
                                $fonwrk[$j] = 'N';
                                $fonwrk[$j + 1] = 'i';
                                $j += 2;
                                $i++;
                            }

                        else
                            $copfon = 1;

                    break;

                case 'o': //se o caracter for o

                    //oS final -> o
                    //oZ final -> o
                    if (($fonaux[$i + 1] == 's') || ($fonaux[$i + 1] == 'Z'))
                        if ($fonaux[$i + 2] == ' ') {
                            $fonwrk[$j] = 'o';
                            break;
                        } //if
                        else
                            $copfon = 1;
                    else
                        $copfon = 1;

                    break;

                case 'P': //se o caracter for P

                    //PH -> F
                    if ($fonaux[$i + 1] == 'H') {
                        $fonwrk[$j] = 'F';
                        $i++;
                        $newmud = 1;
                    } //if
                    else
                        $copmud = 1;

                    break;

                case 'Q': //se o caracter for Q

                    //Koi -> Ki (QUE, QUI -> KE, KI)
                    if ($fonaux[$i + 1] == 'o')
                        if ($fonaux[$i + 2] == 'i') {
                            $fonwrk[$j] = 'K';
                            $j++;
                            $i++;
                            break;
                        } //if

                    //QoA -> KoA (QUA -> KUA)
                    $fonwrk[$j] = 'K';
                    $j++;
                    break;

                case 'R': //se o caracter for R

                    //R nao eh modificado
                    $copfon = 1;

                    break;

                case 's': //se o caracter for s

                    //s final eh ignorado
                    if ($fonaux[$i + 1] == ' ')
                        break;

                    //s inicial + vogal nao eh modificado
                    if (($fonaux[$i + 1] == 'a') || ($fonaux[$i + 1] == 'i') || ($fonaux[$i + 1] == 'o'))
                        if (i == 0) {
                            $copfon = 1;
                            break;
                        } //if
                        else

                            //s entre duas vogais -> z
                            if (($fonaux[$i - 1] != 'a') && ($fonaux[$i - 1] != 'i') && ($fonaux[$i - 1] != 'o')) {
                                $copfon = 1;
                                break;
                            } //if
                            else

                                //SoL nao eh modificado
                                if (($fonaux[$i + 1] == 'o') && ($fonaux[$i + 2] == 'L') && ($fonaux[$i + 3] == ' ')) {
                                    $copfon = 1;
                                    break;
                                } //if
                                else {
                                    $fonwrk[$j] = 'Z';
                                    $j++;
                                    break;
                                } //else

                    //ss -> s
                    if ($fonaux[$i + 1] == 's')
                        if ($fonaux[$i + 2] != ' ') {
                            $copfon = 1;
                            $i++;
                            break;
                        } //if
                        else {
                            $fonaux[$i + 1] = ' ';
                            break;
                        } //else

                    //s inicial seguido de consoante fica precedido de i
                    //se nao for sci, sh ou sch nao seguido de vogal
                    if ($i == 0)
                        if (!(($fonaux[$i + 1] == 'C') && ($fonaux[$i + 2] == 'i')))
                            if ($fonaux[$i + 1] != 'H')
                                if (!(($fonaux[$i + 1] == 'C') && ($fonaux[$i + 2] == 'H')
                                    && (($fonaux[$i + 3] != 'a') && ($fonaux[$i + 3] != 'i')
                                        && ($fonaux[$i + 3] != 'o')))
                                ) {
                                    $fonwrk[$j] = 'i';
                                    $j++;
                                    $copfon = 1;
                                    break;
                                } //if

                    //sH -> X;
                    if ($fonaux[$i + 1] == 'H') {
                        $fonwrk[$j] = 'X';
                        $i++;
                        $newmud = 1;
                        break;
                    } //if

                    if ($fonaux[$i + 1] != 'C') {
                        $copfon = 1;
                        break;
                    } //if

                    //   sCh nao seguido de i torna-se X
                    if ($fonaux[$i + 2] == 'H') {
                        $fonwrk[$j] = 'X';
                        $i += 2;
                        $newmud = 1;
                        break;
                    } //if

                    if ($fonaux[$i + 2] != 'i') {
                        $copfon = 1;
                        break;
                    } //if

                    //sCi final -> Xi
                    if ($fonaux[$i + 3] == ' ') {
                        $fonwrk[$j] = 'X';
                        $fonwrk[$j + 1] = 'i';
                        $i = $i + 3;
                        break;
                    } //if

                    //sCi vogal -> X
                    if (($fonaux[$i + 3] == 'a') || ($fonaux[$i + 3] == 'i') || ($fonaux[$i + 3] == 'o')) {
                        $fonwrk[$j] = 'X';
                        $j++;
                        $i += 2;
                        break;
                    } //if

                    //sCi consoante -> si
                    $fonwrk[$j] = 's';
                    $fonwrk[$j + 1] = 'i';
                    $j += 2;
                    $i += 2;
                    break;

                case 'T': //se o caracter for T

                    //TS -> S
                    if ($fonaux[$i + 1] == 's')
                        break;

                    //TZ -> Z
                    else if ($fonaux[$i + 1] == 'Z')
                        break;

                    else
                        $copmud = 1;

                    break;

                case 'V': //se o caracter for V
                case 'W': //ou se o caracter for W

                    //V,W inicial + vogal -> o + vogal (U + vogal)
                    if ($fonaux[$i + 1] == 'a' || $fonaux[$i + 1] == 'i' || $fonaux[$i + 1] == 'o')
                        if ($i == 0) {
                            $fonwrk[$j] = 'o';
                            $j++;
                        } //if

                        //V,W NAO inicial + vogal -> V + vogal
                        else {
                            $fonwrk[$j] = 'V';
                            $newmud = 1;
                        } //else
                    else {
                        $fonwrk[$j] = 'V';
                        $newmud = 1;
                    }     //else

                    break;

                case 'X': //se o caracter for X

                    //caracter nao eh modificado
                    $copmud = 1;

                    break;

                case 'Y': //se o caracter for Y
                    //Y jah foi tratado acima
                    break;

                case 'Z': //se o caracter for Z

                    //Z final eh eliminado
                    if ($fonaux[$i + 1] == ' ')
                        break;

                    //Z + vogal nao eh modificado
                    else if (($fonaux[$i + 1] == 'a') || ($fonaux[$i + 1] == 'i') || ($fonaux[$i + 1] == 'o'))
                        $copfon = 1;

                    //Z + consoante -> S + consoante
                    else {
                        $fonwrk[$j] = 's';
                        $j++;
                    }    //else

                    break;

                default: //se o caracter nao for um dos jah relacionados

                    //o caracter nao eh modificado
                    $fonwrk[$j] = $fonaux[$i];

                    $j++;
                    break;
            } //switch

            //copia caracter corrente
            if ($copfon == 1) {
                $fonwrk[$j] = $fonaux[$i];
                $j++;
            } //if

            //insercao de i apos consoante muda
            if ($copmud == 1)
                $fonwrk[$j] = $fonaux[$i];

            if ($copmud == 1 || $newmud == 1) {
                $j++;
                $k = 0;

                while ($k == 0)
                    if ($fonaux[$i + 1] == ' ') //e final mudo
                    {
                        $fonwrk[$j] = 'i';
                        $k = 1;
                    } //if

                    else if (($fonaux[$i + 1] == 'a') || ($fonaux[$i + 1] == 'i') || ($fonaux[$i + 1] == 'o'))
                        $k = 1;
                    else if ($fonwrk[$j - 1] == 'X') {
                        $fonwrk[$j] = 'i';
                        $j++;
                        $k = 1;
                    } //if

                    else if ($fonaux[$i + 1] == 'R')
                        $k = 1;

                    else if ($fonaux[$i + 1] == 'L')
                        $k = 1;
                    else if ($fonaux[$i + 1] != 'H') {
                        $fonwrk[$j] = 'i';
                        $j++;
                        $k = 1;
                    } //if
                    else
                        $i++;
            }
        } //for

        for ($i = 0; $i < $len; $i++) { // percorre toda a string

            //i -> I
            if ($fonwrk[$i] == 'i')
                $fonwrk[$i] = 'I';

            else
                //a -> A
                if ($fonwrk[$i] == 'a')
                    $fonwrk[$i] = 'A';

                else

                    //o -> U
                    if ($fonwrk[$i] == 'o')
                        $fonwrk[$i] = 'U';

                    else

                        //s -> S
                        if ($fonwrk[$i] == 's')
                            $fonwrk[$i] = 'S';

                        else

                            //E -> b
                            if ($fonwrk[$i] == 'E')
                                $fonwrk[$i] = ' ';

                            else

                                //Y -> _
                                if ($fonwrk[$i] == 'Y')
                                    $fonwrk[$i] = '_';
        }

        $result = '';

        foreach ($fonwrk as $letra)
            $result .= $letra;

        $result = $this->removeMultiple($result);
        $result = trim(strtoupper($result));
        return $result;
    }
}
