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
 * Class for compute some calculations on date and time
 *
 * Roughly based on Krono, from Tommaso DArgenio
 *
 * @link http://www.phpclasses.org/browse.html/package/943.html KronoClass Home at phpclasses
 *
 */

class MKrono
{

    static private $instance = NULL;
    private $baseDate;
    private $separator;
    private $localeConv;
    private $formatDate;
    private $formatTimestamp;
    private $timeZone;

    public function __construct()
    {
    }

    public static function getInstance()
    {
        if (self::$instance == NULL) {
            self::$instance = new MKrono();
            self::$instance->baseDate = '02/01/00'; // For day/month names
            self::$instance->localeConv = localeConv();
            self::$instance->separator = Manager::getOptions('separatorDate');
            self::$instance->formatDate = Manager::getOptions('formatDate');
            self::$instance->formatTimestamp = Manager::getOptions('formatTimestamp');
            self::$instance->timeZone = Manager::getOptions('timezone');
        }
        return self::$instance;
    }

    /** Function that throw exception with the error message
     * @access private
     * @return void
     */
    private function onError($msg)
    {
        throw new ERuntimeException('[MKrono Error]' . $msg);
    }

    public function getDateTime($datetime, $format = '', $timeZone = '')
    {
        $krono = MKrono::getInstance();
        if (is_string($datetime) && (trim($datetime) != '')) {
            if ($timeZone != '') {
                $dt = DateTime::createFromFormat('!' . ($format ?: $krono->formatDate), $datetime, new DateTimeZone($timeZone));
            } else {
                $dt = DateTime::createFromFormat('!' . ($format ?: $krono->formatDate), $datetime);
            }
        } elseif ($datetime instanceof MDate) {
            $dt = $datetime->getDateTime();
        } elseif ($datetime instanceof DateTime) {
            $dt = $datetime;
        } else {
            $dt = NULL;
        }
        return $dt;
    }

    public function format($datetime, $format = '')
    {
        $krono = MKrono::getInstance();
        $dt = $krono->getDateTime($datetime);
        if ($dt instanceof DateTime) {
            return $dt->format($format ?: $krono->formatDate);
        }
        return NULL;
    }


    /** Convert the name of a day in its numerical value.
     *    + i.e.: Monday stay for 0, Saturday stay for 6
     * @access public
     * @param string $day The name of day, short or long.
     * @return int the number of day
     */
    public function dayToN($day)
    {
        $krono = MKrono::getInstance();
        if ($day == '' || strlen($day) < 3) {
            $krono->onError('dayToN: Day name not valid!');
        }
        $interval = new DateInterval('P1D');
        $d = $krono->getDateTime($krono->baseDate);
        $day = ucwords($day);
        for ($i = 0; $i < 7; $i++) {
            $name = ucwords(strftime('%A', $d->getTimeStamp()));
            if ($day == $name) {
                return $i;
            }
            $d->add($interval);
        }
        return -1;
    }

    /** Convert the numerical value of a day in its name for the setted language by constructor.
     *    + Short o long format is choosed by setting the abbr value to true o false
     * @access public
     * @param int $day The number of day, 0 stay for Sunday and 6 for Saturday
     * @return string the name of day in language setted by constructor
     */
    public function NToDay($day)
    {
        $krono = MKrono::getInstance();
        if ($day > 6 || $day < 0) {
            $krono->onError('Day range not valid. Must be 0 to 6!');
        }
        $d = $krono->getDateTime($krono->baseDate);
        $interval = new DateInterval('P' . $day . 'D');
        return ucwords(strftime('%A', $d->add($interval)->getTimeStamp()));
    }

    /** Convert the name of a month in its numerical value.
     *    + i.e.: February stay for 2, December stay for 12
     * @access public
     * @param string $month The name of month, short or long format, in language setted by constructor
     * @return int The number rappresenting the month
     */
    public function monthToN($month)
    {
        $krono = MKrono::getInstance();
        if ($month == '' || strlen($month) < 3) {
            $krono->onError('Month name not valid!');
        }
        $interval = new DateInterval('P1M');
        $month = ucwords($month);
        $d = $krono->getDateTime($krono->baseDate);
        for ($i = 1; $i < 13; $i++) {
            $name = ucwords(strftime('%B', $d->getTimeStamp()));
            if ($month == $name) {
                return $i;
            }
            $d->add($interval);
        }
        return -1;
    }


    public function listMonths()
    {
        $krono = MKrono::getInstance();
        $months = array();
        for ($i = 1; $i < 13; ++$i) {
            $months[$i] = $krono->NToMonth($i);
        }

        return $months;
    }

    /** Convert the numerical value of a month in its name.
     *    + Short o long format is choosed by setting the abbr value to true o false
     * @access public
     * @param string $month The number of month
     * @return string The name of month in language setted by constructor
     */
    public function NToMonth($month)
    {
        $krono = MKrono::getInstance();
        if ($month > 12 || $month < 1) {
            $krono->onError('Month range not valid. Must be 1 to 12!');
        }
        $d = $krono->getDateTime($krono->baseDate);
        $interval = new DateInterval('P' . --$month . 'M');
        return ucwords(strftime('%B', $d->add($interval)->getTimeStamp()));
    }

    /** Define if the day of date given is equal to day given.
     *    + Is Friday the 22nd of November 2002 ?
     *    + date according to dateFormat parameter passed on inizialization
     * @access public
     * @param date $data The date to check
     * @param string $day The name of day to check
     * @return mixed 1 if check is true, otherwise the day of date
     */
    public function isDay($date, $day)
    {
        $krono = MKrono::getInstance();
        if (is_numeric($day)) {
            $day = $krono->NToDay($day);
        }
        $d = $krono->getDateTime($date);
        $name = ucwords(strftime('%A', $d->getTimeStamp()));
        return ($day == $name);
    }

    public function dateDiff($dateA, $dateB, $format = '%a')
    {
        $krono = MKrono::getInstance();
        $dA = $krono->getDateTime($dateA);
        $dB = $krono->getDateTime($dateB);
        $interval = $dA->diff($dB);
        $days = $interval->format($format);
        if ($interval->h > 1) { // data of dst beginning
            $days++;
        }
        return $days;
    }

    /** Define what's the day difference between two given date.
     *    + date according to dateFormat parameter passed on inizialization
     * @access public
     * @param date $dateA The start date
     * @param date $dateB The end date
     * @return int The difference in days between the two given dates
     */
    public function daysDiff($dateA, $dateB)
    {
        $krono = MKrono::getInstance();
        return $krono->dateDiff($dateA, $dateB);
    }

    /** Define what's the week difference between two given date.
     *    + date according to dateFormat parameter passed on inizialization
     * @access public
     * @param date $dateA The start date
     * @param date $dateB The end date
     * @return int The difference in weeks between the two given dates
     */
    public function weeksDiff($dateA, $dateB)
    {
        $krono = MKrono::getInstance();
        $days = $krono->dateDiff($dateA, $dateB);
        return (int)($days / 7);
    }

    /** Define what's the month difference between two given date.
     *    + date according to dateFormat parameter passed on inizialization
     * @access public
     * @param date $dateA The start date
     * @param date $dateB The end date
     * @return int The difference in months between the two given dates
     */
    public function monthsDiff($dateA, $dateB)
    {
        $krono = MKrono::getInstance();
        $days = $krono->dateDiff($dateA, $dateB);
        return (int)($days / 30);
    }

    /** Define what's the year difference between two given date.
     *    + date according to dateFormat parameter passed on inizialization
     * @access public
     * @param date $dateA The start date
     * @param date $dateB The end date
     * @return int The difference in years between the two given dates
     */
    public function yearsDiff($dateA, $dateB)
    {
        $krono = MKrono::getInstance();
        $days = $krono->dateDiff($dateA, $dateB);
        return (int)($days / 365);
    }

    /**
     *    Give the difference between two times.
     *    + (i.e.: how minutes from 4.50 to 12.50?).
     *    + Accept only 24H format.
     *    + the time is a string like: "4.50" or "4:50"
     * @access public
     * @param string $time_from The start time
     * @param string $time_to The end time
     * @param char $result_in The format of result
     *    + "m" -> for minutes
     *    + "s" -> for seconds
     *    + "h" -> for hours
     * @return string The difference between times according to format given in $result_in
     */
    public function timesDiff($time_from, $time_to, $result_in = "m")
    {
        $krono = MKrono::getInstance();
        if ((strstr($time_from, '.') || strstr($time_from, ':')) && (strstr($time_to, '.') || strstr($time_to, ':'))) {
            $time_from = str_replace(':', '.', $time_from);
            $time_to = str_replace(':', '.', $time_to);

            $t1 = explode('.', $time_from);
            $t2 = explode('.', $time_to);

            $h1 = $t1[0];
            $m1 = $t1[1];

            $h2 = $t2[0];
            $m2 = $t2[1];

            if ($h1 <= 24 && $h2 <= 24 && $h1 >= 0 && $h2 >= 0 && $m1 <= 59 && $m2 <= 59 && $m1 >= 0 && $m2 >= 0) {
                $diff = ($h2 * 3600 + $m2 * 60) - ($h1 * 3600 + $m1 * 60);
                if ($result_in == "s") {
                    return $diff;
                } elseif ($result_in == "m") {
                    return $diff / 60;
                } elseif ($result_in == "h") {
                    $r = $diff / 3600;
                    $t = explode('.', $r);
                    $h = $t[0];
                    if ($h > 24)
                        $h -= 24;
                    $m = round("0.$t[1]" * 60);
                    return $h . 'h' . $m . 'm';
                }
            } else {
                $krono->onError('timesDiff: Time range not valid. Must be 0 to 24 for hours and 0 to 59 for minutes!');
            }
        } else {
            $krono->onError('timesDiff: Time format not valid. Must be in format HH:mm or HH.mm');
        }
    }

    public function dateAdd($datetime, $interval)
    {
        $krono = MKrono::getInstance();
        return $krono->getDateTime($datetime)->add(new DateInterval($interval));
    }

    public function dateSub($datetime, $interval)
    {
        $krono = MKrono::getInstance();
        return $krono->getDateTime($datetime)->sub(new DateInterval($interval));
    }

    /**
     *    Add some minutes or hours to a given time.
     *    + i.e.: (add 2 hours to 14.10 -> result is 16.10)
     *    + Accept only 24H format.
     *    + the time is a string like: "4.50" or "4:50"
     * @param string $time The time string to transform
     * @param int $add The hours or minutes to add
     * @param char $what is what add to time
     *    + "m" -> for add minutes
     *    + "h" -> for add hours
     *    + "t" -> for add time string given in HH:mm format
     * @return string Result is in format HH:mm, return -1 on error
     */
    public function timesAdd($time, $add, $what)
    {
        $krono = MKrono::getInstance();
        $point = $krono->localeConv['mon_decimal_point'];
        if ((strstr($time, '.') || strstr($time, ':'))) {
            $time = str_replace(':', '.', $time);
            $t1 = explode('.', $time);
            $h1 = $t1[0];
            $m1 = $t1[1];
            if ($h1 <= 24 && $h1 >= 0 && $m1 <= 59 && $m1 >= 0) {
                if ($what == "m") {
                    $res = ($h1 * 60) + $m1 + $add;
                    $r = $res / 60;
                    $r = trim($r);
                    $t = explode($point, $r);
                    $h = $t[0];
                    if ($h > 24)
                        $h -= 24;
                    $m = round("0.$t[1]" * 60);
                    return $h . ':' . $m;
                } elseif ($what == "h") {
                    $res = ($h1 * 60) + $m1 + ($add * 60);
                    $r = $res / 60;
                    $t = explode($point, $r);
                    $h = $t[0];
                    if ($h > 24)
                        $h -= 24;
                    $m = round("0.$t[1]" * 60);
                    return $h . ':' . $m;
                } elseif ($what == "t") {
                    if ((strstr($add, '.') || strstr($add, ':'))) {
                        $add = str_replace(':', '.', $add);
                        $t1 = explode('.', $add);
                        $h2 = $t1[0];
                        $m2 = $t1[1];
                        if ($h2 <= 24 && $h2 >= 0 && $m2 <= 59 && $m2 >= 0) {
                            $res = ($h1 * 60) + ($h2 * 60) + $m1 + $m2;
                            $r = $res / 60;
                            $t = explode($point, $r);
                            $h = $t[0];
                            if ($h > 24)
                                $h -= 24;
                            $m = round("0.$t[1]" * 60);
                            return $h . ':' . $m;
                        }
                    } else {
                        $krono->onError('timeasAdd: Time format not valid. Must be in format HH:mm or HH.mm');
                    }
                }
            } else {
                $krono->onError('timesAdd: Time range not valid. Must be 0 to 24 for hours and 0 to 59 for minutes!');
            }
        } else {
            $krono->onError('timesAdd: Time format not valid. Must be in format HH:mm or HH.mm');
        }
    }


    public function getPeriod($dateA, $interval, $dateB)
    {
        $krono = MKrono::getInstance();
        $initial = $krono->getDateTime($dateA);
        $final = $krono->getDateTime($dateB);
        $int = new DateInterval($interval);
        return new DatePeriod($initial, $int, $final);
    }

    /** Define how days left to given date. date according to dateFormat parameter passed on inizialization
     * @access public
     * @param date $date The date in traditional format for calculating diff
     * @return int The amount of days between today and given date
     */
    public function howTo($date)
    {
        $today = new MDate(Manager::getSysDate());
        return $today->diff($date, '%a');
    }

    /**
     *    Encaps PHP getdate() function
     */
    public function getDate($timestamp)
    {
        $ts = new MTimeStamp($timestamp);
        return getdate($ts->getTimestamp());
    }

    /**
     * Function to turn seconds into a time
     * + added by tim@trundlie.fsnet.co.uk on 08/21/2003
     * + i.e. 30600sec is 8.30am
     * +        63000sec is 17:30
     * @access public
     * @param int $secs number of seconds to be converted to time of day.
     * @return string The seconds converted into time
     */
    public function secsToTime($secs)
    {
        if ($secs == 0) {
            return "-empty-";
        } else {
            $krono = MKrono::getInstance();
            $point = $krono->localeConv['mon_decimal_point'];
            $r = $secs / 60;
            $r = trim($r);
            $t = explode($point, $r);
            $s = round("0.$t[1]" * 60);
            if ($s == 0) { // tidy up output
                $s = '00';
            }
            $r = $t[0] / 60;
            $r = trim($r);
            $t = explode($point, $r);
            $m = round("0.$t[1]" * 60);
            if ($m == 0) { // tidy up output
                $m = '00';
            }
            $h = $t[0];
            return $h . ':' . $m . ':' . $s;
        }
    }

    /**
     *  Function that check the validity of a date and/or time
     *  + in according with dateFormat and timeFormat
     *  + suggested by Vincenzo Visciano <liberodicrederci@yahoo.it>
     * @access public
     * @param string $date The date and/or time to check validity of
     * @return bool True if is all ok, False is all wrong, -1 if only date is wrong, -2 if only time is wrong
     */
    public function isValid($date)
    {
        $krono = MKrono::getInstance();
        $date = str_replace('-', $krono->separator, $date);
        $date = str_replace('.', $krono->separator, $date);
        list($day, $month, $year) = explode($krono->separator, $date, 3);
        return checkdate($month, $day, $year);
    }

    public function invertDate($date)
    {
        $krono = MKrono::getInstance();
        $date = str_replace('-', $krono->separator, $date);
        $date = str_replace('.', $krono->separator, $date);
        list($obj1, $obj2, $obj3) = preg_split("#{$krono->separator}#", $date, 3);
        $date = $obj3 . $krono->separator . $obj2 . $krono->separator . $obj1;
        if (($date == ($krono->separator . $krono->separator))) {
            $date = 'Invalid Date!';
        }
        return $date;
    }

    public function compareDate($dateA, $operator, $dateB)
    {
        $krono = MKrono::getInstance();
        $A = $krono->getDateTime($dateA);
        $B = $krono->getDateTime($dateB);
        $gt = ($A > $B);
        $lt = ($A < $B);
        $eq = ($A == $B);
        switch ($operator) {
            case '==':
                return $eq;
                break;
            case '=':
                return $eq;
                break;
            case '>':
                return $gt;
                break;
            case '<':
                return $lt;
                break;
            case '>=':
                return $eq || $gt;
                break;
            case '<=':
                return $eq || $lt;
                break;
            case '!=':
                return $lt || $gt;
                break;
            case '<>':
                return $lt || $gt;
                break;
        }
    }

    public function getDay($date, $format = 'd')
    {
        $d = ($date instanceof MDate) ? $date : new MDate($date);
        return $d->format($format);
    }

    public function getMonth($date, $format = 'm')
    {
        $d = ($date instanceof MDate) ? $date : new MDate($date);
        return $d->format($format);
    }

    public function getYear($date, $format = 'Y')
    {
        $d = ($date instanceof MDate) ? $date : new MDate($date);
        return $d->format($format);
    }

    public function between($date1, $date, $date2)
    {
        $krono = MKrono::getInstance();
        $num1 = str_replace("/", "", $krono->invertDate($date1));
        $num = str_replace("/", "", $krono->invertDate($date));
        $num2 = str_replace("/", "", $krono->invertDate($date2));
        return (($num1 <= $num) && ($num <= $num2)) ? TRUE : FALSE;
    }

    public function getLastDayOfMonth($month, $year = NULL)
    {
        $year = $year ?: date('Y');
        return cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }

}
