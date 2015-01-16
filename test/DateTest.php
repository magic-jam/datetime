<?php
use mj\datetime\Date;

class DateTest extends PHPUnit_Framework_TestCase
{
	public function testNativeFromNative() {
		$d = new Date(new \DateTime("2013-12-25T15:14:13"));
		$this->assertDate($d, 2013, 12, 25);
	}

	public function testNativeFromString() {
		$d = new Date("2015-02-13T15:14:13");
		$this->assertDate($d, 2015, 2, 13);
	}

	// TODO(jwf): native + timezone

	public function testYMD() {
		$d = new Date(2015, 1, 15);
		$this->assertDate($d, 2015, 1, 15);
	}

	// TODO(jwf): ymd + timezone

	public function testYMDHIS() {
		$d = new Date(2015, 1, 15, 16, 42, 31);
		$this->assertDate($d, 2015, 1, 15);	
	}

	// TODO(jwf): ymdhis + timezone

	protected function assertDate($dt, $y, $m, $d) {
		$this->assertEquals($dt->year(), $y);
		$this->assertEquals($dt->month(), $m);
		$this->assertEquals($dt->day(), $d);
		$this->assertEquals($dt->hours(), 0);
		$this->assertEquals($dt->minutes(), 0);
		$this->assertEquals($dt->seconds(), 0);
	}
}
?>