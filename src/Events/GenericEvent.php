<?php

namespace ChaoticWave\BlueVelvent\Events;

use Illuminate\Queue\SerializesModels;

class GenericEvent
{
	use SerializesModels;

	/**
	 * @var string
	 */
	public $name;
	/**
	 * @var array
	 */
	public $payload;

	/**
	 * @param string $name
	 * @param array  $payload
	 */
	public function __construct(string $name, array $payload)
	{
		$this->name = $name;
		$this->payload = $payload;
	}
}
