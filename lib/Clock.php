<?php
namespace mj\datetime;

class Clock
{
	private static $clock = null;

	public static function defaultClock() {
		if (self::$clock === null) {
			self::$clock = new WallClock;
		}
		return self::$clock;
	}

	public static function setDefaultClock(ClockSource $clock) {
		self::$clock = $clock;
	}

	public static function clock() {
		return self::defaultClock();
	}

	public static function now() {
		return self::defaultClock()->now();
	}

	public static function today() {
		return self::defaultClock()->today();
	}
}
