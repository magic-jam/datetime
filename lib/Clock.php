<?php
namespace mj\datetime;

class Clock
{
	private static $defaultClock = null;

	public static function defaultClock() {
		if (self::$defaultClock === null) {
			self::$defaultClock = new WallClock;
		}
		return self::$defaultClock;
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
