<?php
namespace mj\datetime;

class FixedClock implements ClockSource
{
	private $now;

	public function __construct() {
		$this->now = Clock::now();
	}

	public function setNow(DateTime $now) {
		$this->now = $now;
	}

	public function now() {
		return $this->now;
	}

	public function today() {
		return $this->now->toDate();
	}
}