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

/*
  ====================
  Format
  ====================
  The format that the passed in string should be in.
  See the formatting options below. In most cases, the same letters as for the date() can be used.

  The following characters are recognized in the format parameter string
  ------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  format character    Description                                                                 Example parsable values
  ------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  Day 	            --- 	                                                                    ---
  ------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  d and j 	        Day of the month, 2 digits with or without leading zeros 	            01 to 31 or 1 to 31
  D and l 	        A textual representation of a day 	                                    Mon through Sun or Sunday through Saturday
  S 	                English ordinal suffix for the day of the month, 2 characters.              st, nd, rd or th.
  z                	The day of the year (starting from 0) 	                                    0 through 365
  ------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  Month 	            --- 	                                                                    ---
  ------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  F and M 	        A textual representation of a month, such as January or Sept 	            January through December or Jan through Dec
  m and n 	        Numeric representation of a month, with or without leading zeros 	    01 through 12 or 1 through 12
  ------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  Year 	            --- 	                                                                    ---
  ------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  Y 	                A full numeric representation of a year, 4 digits 	                    Examples: 1999 or 2003
  y 	                A two digit representation of a year 	                                    Examples: 99 or 03
  ------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  Time 	            --- 	                                                                    ---
  ------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  a and A 	        Ante meridiem and Post meridiem 	                                    am or pm
  g and h 	        12-hour format of an hour with or without leading zero 	                    1 through 12 or 01 through 12
  G and H 	        24-hour format of an hour with or without leading zeros 	            0 through 23 or 00 through 23
  i 	                Minutes with leading zeros 	                                            00 to 59
  s 	                Seconds, with leading zeros 	                                            00 through 59
  u 	                Microseconds (up to six digits) 	                                    Example: 45, 654321
  ------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  Timezone 	        --- 	                                                                    ---
  ------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  e, O, P and T 	    Timezone identifier, or difference to UTC in hours, or difference       Examples: UTC, GMT, Atlantic/Azores or +0200 or +02:00 or EST, MDT
  to UTC with colon between hours and minutes, or timezone abbreviation
  ------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  Full Date/Time 	    --- 	                                                                    ---
  ------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  U 	                Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT) 	            Example: 1292177455
  ------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  Whitespace and Separators 	--- 	---
  ------------------------------------------------------------------------------------------------------------------------------------------------------------------------
  (space) 	        One space or one tab 	Example:
  # 	                One of the following separation symbol: ;, :, /, ., ,, -, ( or ) 	    Example: /
  ;, :, /, ., ,,      The specified character.                                                    Example: -
  -, ( or )
  ? 	                A random byte 	                                                            Example: ^ (Be aware that for UTF-8 characracters you might need
  more than one ?. In this case, using * is probably what you want instead)
 * 	                Random bytes until the next separator or digit 	                            Example: * in Y-*-d with the string 2009-aWord-08 will match aWord
  ! 	                Resets all fields (year, month, day, hour, minute, second,                  Without !, all fields will be set to the current date and time.
  fraction and timzone information) to the Unix Epoch 	                    Y-m-d| will set the year, month and day to the information found
  | 	                Resets all fields (year, month, day, hour, minute, second,                  in the string to parse, and sets the hour, minute and second to 0.
  fraction and timzone information) to the Unix Epoch
  if they have not been parsed yet
  + 	                If this format specifier is present, trailing data in the string            Use DateTime::getLastErrors() to find out whether trailing data was present.
  will not cause an error, but a warning instead
  ------------------------------------------------------------------------------------------------------------------------------------------------------------------------

  Unrecognized characters in the format string will cause the parsing to fail and an error message is appended to the returned structure. You can query error messages with DateTime::getLastErrors().
  If format does not contain the character ! then portions of the generated time which are not specified in format will be set to the current system time.
  If format contains the character !, then portions of the generated time not provided in format, as well as values to the left-hand side of the !, will be set to corresponding values from the Unix epoch.
  The Unix epoch is 1970-01-01 00:00:00 UTC.

  ====================
  Period
  ====================
  Interval specification.
  The format starts with the letter P, for "period." Each duration period is represented by an integer value followed by a period designator.
  If the duration contains time elements, that portion of the specification is preceded by the letter T.
  Here are some simple examples. Two days is P2D. Two seconds is PT2S. Six years and five minutes is P6YT5M.

  Period Description
  Y      years
  M      months
  D      days
  W      weeks. These get converted into days, so can not be combined with D.
  H      hours
  M      minutes
  S      seconds
 */

class MDate extends MType
{

    private $datetime;
    private $format;
    private $separator = '/';

    public function __construct($datetime = NULL, $format = '')
    {
        parent::__construct();
        $this->separator = Manager::getOptions('separatorDate');
        $this->format = ($format ?: Manager::getOptions('formatDate'));
        $this->datetime = MKrono::getDateTime($datetime, $this->format);
    }

    public function __call($name, $arguments)
    {
        if ($arguments) {
            return $this->datetime->$name($arguments);
        }
        return $this->datetime->$name();
    }

    public static function getSysDate($format = 'd/m/Y')
    {
        return new MDate(date($format));
    }

    public static function create($date = '01/01/01')
    {
        return new MDate($date);
    }

    public function getDateTime()
    {
        return $this->datetime;
    }

    public function getValue()
    {
        return $this->datetime;
    }

    public function copy()
    {
        return clone $this;
    }

    public function format($format = '')
    {
        return MKrono::format($this, $format ?: $this->format);
    }

    public function invert()
    {
        $date = $this->format();
        return MKrono::invertDate($date);
    }

    public function add($interval)
    {
        MKrono::dateAdd($this->datetime, $interval);
        return $this;
    }

    public function sub($interval)
    {
        MKrono::dateSub($this->datetime, $interval);
        return $this;
    }

    public function diff($date, $format = '%a')
    {
        return MKrono::dateDiff($this->datetime, $date, $format);
    }

    public function getPeriod($dateInitial, $interval, $dateFinal)
    {
        return MKrono::getPeriod($dateInitial, $interval, $dateFinal);
    }

    public function compare($operator, $date)
    {
        return MKrono::compareDate($this->datetime, $operator, $date);
    }

    public function getDay($format = 'd')
    {
        return MKrono::getDay($this, $format);
    }

    public function getMonth($format = 'm')
    {
        return MKrono::getMonth($this, $format);
    }

    public function getYear($format = 'Y')
    {
        return MKrono::getYear($this, $format);
    }

    public function getDayNick()
    {
        return strftime('%a', $this->datetime->getTimeStamp());
    }

    public function getDayName()
    {
        return strftime('%A', $this->datetime->getTimeStamp());
    }

    public function getMonthNick()
    {
        return strftime('%b', $this->datetime->getTimeStamp());
    }

    public function getMonthName()
    {
        return strftime('%B', $this->datetime->getTimeStamp());
    }

    public function getFullName($dayOfWeek = false)
    {
        $locale = \Manager::getOptions('locale');
        $prefix = ($dayOfWeek ? $this->getDayName() . ', ' : '');
        if ($locale[0] == 'pt_BR') {
            return $prefix . $this->getDay('j') . ' de ' . $this->getMonthName() . ' de ' . $this->getYear();
        } else {
            return $prefix . $this->getMonthName() . ' ' . $this->getDay('j') . ',' . $this->getYear();
        }
    }

    public function getPlainValue()
    {
        return $this->format();
    }

    public function __toString()
    {
        return $this->format();
    }

    public function isValid()
    {
        return MKrono::isValid($this->format());
    }

}
