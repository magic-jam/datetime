<?php
namespace mj\datetime;

/**
 * BasePHP's Date/Date_Time classes wrap around PHP's own DateTime implementation whose
 * API, IMO, is ghastly.
 *
 * Main differences:
 *   1. This implementation is immutable
 *   2. Accessor methods for component parts
 *   3. Distinction between date and date-and-time 
 *
 * @todo add/subtract intervals
 * @todo get interval between two dates
 */
class Date
{
    //
    // Default

    private static $defaultTimezone     = null;
    private static $utcTimezone         = null;

    public static function defaultTimezone() {
        if (self::$defaultTimezone === null) {
            self::$defaultTimezone = new \DateTimeZone(date_default_timezone_get());
        }
        return self::$defaultTimezone;
    }

    public static function utcTimezone() {
        if (self::$utcTimezone === null) {
            self::$utcTimezone = new \DateTimeZone('UTC');
        }
        return self::$utcTimezone;
    }

    //
    // Safe Parsing

    // public static function parseDate($value) {
    //     if ($value === null) {
    //         return null;
    //     } elseif ($value instanceof \mj\datetime\DateTime) {
    //         return $value->toDate();
    //     } elseif ($value instanceof \mj\datetime\Date) {
    //         return $value;
    //     } else {
    //         try {
    //             return new Date($value);
    //         } catch (\Exception $e) {
    //             return null;
    //         }
    //     }
    // }

    // public static function parseDateTime($value) {
    //     if ($value === null) {
    //         return null;
    //     } elseif ($value instanceof \mj\datetime\DateTime) {
    //         return $value;
    //     } elseif ($value instanceof \mj\datetime\Date) {
    //         return $value->toDateTime();
    //     } else {
    //         try {
    //             return new DateTime($value);
    //         } catch (\Exception $e) {
    //             return null;
    //         }
    //     }
    // }

   // public static function fromRequest($value) {
   //     $class = get_called_class();
   //     try {
   //         if (empty($value)) { // nothing submitted
   //             return null;
   //         } elseif (is_array($value)) {
   //             if (isset($value['year'])) {
   //                 if (isset($value['month']) && isset($value['day'])) {
   //                     $args = array($value['year'], $value['month'], $value['day']);
   //                     if (isset($value['hours']) && isset($value['minutes']) && isset($value['seconds'])) {
   //                         $args[] = $value['hours'];
   //                         $args[] = $value['minutes'];
   //                         $args[] = $value['seconds'];
   //                         if (isset($value['timezone'])) {
   //                             $args[] = $value['timezone'];
   //                         }
   //                     }
   //                     return new $class($args);
   //                 } else {
   //                     return null;
   //                 }
   //             } else {
   //                 return new $class($value);
   //             }
   //         } else { // assume string
   //             if (is_numeric($value)) $value = '@' . $value; // unix timestamp
   //             return new $class($value);
   //         }
   //     } catch (InvalidArgumentException $e) {
   //         return null;
   //     }
   // }

    //
    // These have just been lifted from PHP's DateTime
    
    const ATOM     = 'Y-m-d\TH:i:sP';
    const COOKIE   = 'l, d-M-y H:i:s T';
    const ISO8601  = 'Y-m-d\TH:i:sO';
    const RFC822   = 'D, d M y H:i:s O';
    const RFC850   = 'l, d-M-y H:i:s T';
    const RFC1036  = 'D, d M y H:i:s O';
    const RFC1123  = 'D, d M Y H:i:s O';
    const RFC2822  = 'D, d M Y H:i:s O';
    const RFC3339  = 'Y-m-d\TH:i:sP';
    const RSS      = 'D, d M Y H:i:s O';
    const W3C      = 'Y-m-d\TH:i:sP';
    
    //
    // Some variations
    
    const ISO8601_DATE                      = 'Y-m-d';
    const ISO8601_DATE_TIME                 = 'Y-m-d\TH:i:s';
    const ISO8601_DATE_TIME_WITH_TIMEZONE   = 'Y-m-d\TH:i:sO';

    protected $y, $m, $d, $h, $i, $s;
    protected $native;
    protected $timezone;
    protected $timezoneName; // for use during serialization

    public function __construct($y, $m = null, $d = null, $h = null, $i = null, $s = null, $tz = null) {
        $argc = func_num_args();
        switch ($argc) {
            case 1: // natively parseable
                $this->initFromNative(($y instanceof \DateTime) ? $y : new \DateTime($y));
                break;
            case 2: // natively parseable + native TimeZone
                $this->initFromNative(new \DateTime($y, $m));
                break;
            case 3: // y/m/d
                $this->setDate($y, $m, $d);
                $this->setTime(0, 0, 0);
                $this->setTimezone(null);
                $this->generateNative();
                break;
            case 4: // y/m/d + timezone
                $this->setDate($y, $m, $d);
                $this->setTime(0, 0, 0);
                $this->setTimezone($h);
                $this->generateNative();
                break;
            case 6: // y/m/d h:i:s
                $this->setDate($y, $m, $d);
                $this->setTime($h, $i, $s);
                $this->setTimezone(null);
                $this->generateNative();
                break;
            case 7: // y/m/d h:i:s + timezone
                $this->setDate($y, $m, $d);
                $this->setTime($h, $i, $s);
                $this->setTimezone($tz);
                $this->generateNative();
                break;
            default:
                throw new InvalidArgumentException();
        }
    }

    public function year() { return $this->y; }
    public function month() { return $this->m; }
    public function day() { return $this->d; }
    public function hours() { return $this->h; }
    public function minutes() { return $this->i; }
    public function seconds() { return $this->s; }
    public function timezone() { return $this->timezone; }

    public function weekday() { return (int) $this->native->format('w'); }
    public function isLeapYear() { return (bool) $this->native->format('L'); }
    public function daysInMonth() { return (int) $this->native->format('t'); }
    public function isUTC() { return $this->timezone->getName() == 'UTC'; }
    public function timestamp() { return $this->native->getTimestamp(); }
    public function format($f) { return $this->native->format($f); }

    public function isoDate() { return $this->native->format(self::ISO8601_DATE); }
    public function isoDateTime() { return $this->native->format(self::ISO8601_DATE_TIME); }
    public function isoDateTimeWithTimezone() { return $this->native->format(self::ISO8601_DATE_TIME_WITH_TIMEZONE); }

    public function toDate() { return $this; }
    public function toDateTime() { return new DateTime($this->native); }

    public function toUTC() { return $this->to_timezone(self::utcTimezone()); }

    public function toTimezone() {
        $tz = is_string($tz) ? new DateTimeZone($tz) : $tz;
        if ($tz->getName() == $this->timezone->getName()) {
            return $this;
        } else {
            $dt = clone $this->native;
            $dt->setTimezone($tz);
            $class = get_class($this);
            return new $class($dt);
        }
    }

    public function __toString() { return $this->isoDateTimeWithTimezone(); }

    //
    // Serialization

    public function __sleep() {
        $this->timezoneName = $this->timezone->getName();
        return ['y', 'm', 'd', 'h', 'i', 's', 'timezoneName'];
    }

    public function __wakeup() {
        $this->setTimezone($this->timezoneName);
        $this->generateNative();
    }

    //
    // Internal

    protected function adjustNativeTime($native) {
        $native->setTime(0, 0, 0);
    }

    protected function initFromNative($native) {
        $this->adjustNativeTime($native);
        $this->native = $native;
        $this->y = (int) $this->native->format('Y');
        $this->m = (int) $this->native->format('m');
        $this->d = (int) $this->native->format('d');
        $this->h = (int) $this->native->format('H');
        $this->i = (int) $this->native->format('i');
        $this->s = (int) $this->native->format('s');
        $this->timezone = $this->native->getTimezone();
    }

    protected function setDate($y, $m, $d) {
        if (!checkdate($m, $d, $y)) {
            throw new InvalidArgumentException("invalid date: $y-$m-$d");
        }
        $this->y = (int) $y;
        $this->m = (int) $m;
        $this->d = (int) $d;
    }

    protected function setTime($h, $i, $s) {
        $this->h = 0;
        $this->i = 0;
        $this->s = 0;
    }

    protected function setTimezone($tz) {
        if ($tz === null) {
            $tz = self::defaultTimezone();
        } elseif (is_string($tz)) {
            $tz = new \DateTimeZone($tz);
        }
        if (($tz instanceof \DateTimeZone)) {
            $this->timezone = $tz;
        } else {
            throw new InvalidArgumentException("invalid timezone");
        }
    }

    protected function generateNative() {
        $this->native = new DateTime(
            sprintf(
                "%04d-%02d-%02dT%02d:%02d:%02d",
                $this->y, $this->m, $this->d,
                $this->h, $this->i, $this->s
            ),
            $this->timezone
        );
    }

//    /**
 //    * Compares this date with another.
 //    * There's probably a better algorithm for this.
 //    *
 //    * @param $r date to compare with
 //    * @return 0 if the argument date is equal to this date, -1 if this date
 //    *         is before the date argument, and 1 if this date is after the
 //    *         date argument
 //    */
 //    public function compareTo(Date $r) {
        
 //        $l = $this;
 //        $r = $r->toTimezone($this->timezone);
        
 //        if ($l->y > $r->y) return 1; elseif ($l->y < $r->y) return -1;
 //        if ($l->m > $r->m) return 1; elseif ($l->m < $r->m) return -1;
 //        if ($l->d > $r->d) return 1; elseif ($l->d < $r->d) return -1;
 //        if ($l->h > $r->h) return 1; elseif ($l->h < $r->h) return -1;
 //        if ($l->i > $r->i) return 1; elseif ($l->i < $r->i) return -1;
 //        if ($l->s > $r->s) return 1; elseif ($l->s < $r->s) return -1;
 
 //        return 0;
         
 //    }
 
}
