<?php
namespace mj\datetime;

class WallClock implements ClockSource
{
	public function today() {
		return new \mj\datetime\Date(new \DateTime);
	}

	public function now() {
		return new \mj\datetime\DateTime(new \DateTime);
	}
}
