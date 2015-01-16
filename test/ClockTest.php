<?php
use mj\datetime\Clock;
use mj\datetime\WallClock;
use mj\datetime\DateTime;
use mj\datetime\Date;

class ClockTest extends PHPUnit_Framework_TestCase
{
	public function testClockClockReturnsWallClock() {
		$this->assertTrue(Clock::clock() instanceof WallClock);
	}

	public function testClockNowReturnsInstanceOfDateTime() {
		$this->assertTrue(Clock::now() instanceof DateTime);
	}

	public function testClockTodayReturnsInstanceOfDate() {
		$this->assertTrue(Clock::today() instanceof Date);
	}
}