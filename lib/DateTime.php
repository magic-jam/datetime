<?php
namespace mj;

class DateTime extends \mj\Date
{
	public function toDate() { return new Date($this->native); }
    public function toDateTime() { return $this; }
    
    protected function setNative(DateTime $dt) {
        $this->native = $dt;
    }
    
    protected function setTime($h, $i, $s) {
        if ($h == 24 && $i == 0 && $s == 0) $h = 0;
        if ($h < 0 || $h > 23 || $i < 0 || $i > 59 || $s < 0 || $s > 59) {
            throw new InvalidArgumentException("invalid time: $h:$i:$s");
        }
        $this->h = (int) $h;
        $this->i = (int) $i;
        $this->s = (int) $s;
    }
}
