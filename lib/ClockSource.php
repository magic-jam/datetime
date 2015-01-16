<?php
namespace mj\datetime;

interface ClockSource
{
	public function today();
	public function now();
}