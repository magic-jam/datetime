<?php
use mj\datetime\Clock;
use mj\datetime\Date;

class DateTest extends PHPUnit_Framework_TestCase
{
	public function testConstructorWithComponents() {
		$d = new Date(2015, 1, 15);
		$this->assertDate($d, 2015, 1, 15);
	}

	protected function assertDate($dt, $y, $m, $d) {
		$this->assertEquals($dt->year(), 2015);
		$this->assertEquals($dt->month(), 1);
		$this->assertEquals($dt->day(), 15);
		$this->assertEquals($dt->hours(), 0);
		$this->assertEquals($dt->minutes(), 0);
		$this->assertEquals($dt->seconds(), 0);
	}
}
?>