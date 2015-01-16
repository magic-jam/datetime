<?php
namespace mj\datetime;

class Interval
{
	public static function fromNative(\DateInterval $native) {
		$interval = new self;
		$interval->native = $native;
		$interval->initialize();
		return $interval;
	}

	public static function years($y) { return new self(abs($y), 0, 0, 0, 0, 0, $y < 0); }
	public static function months($m) { return new self(0, abs($m), 0, 0, 0, 0, $m < 0); }
	public static function days($d) { return new self(0, 0, abs($d), 0, 0, 0, $d < 0); }
	public static function hours($m) { return new self(0, 0, 0, abs($h), 0, 0, $h < 0); }
	public static function minutes($m) { return new self(0, 0, 0, 0, abs($m), 0, $m < 0); }
	public static function seconds($m) { return new self(0, 0, 0, 0, 0, abs(s), $s < 0); }

	//
	//

	private $native;

	public function __construct($y = null, $m = null, $d = null, $h = null, $i = null, $s = null, $negate = null) {
		if ($arg0 == null) {
			return;
		}
	}

	public function getNativeInterval() {
		return $this->native;
	}

	private function initialize() {

	}
}
