<?php

namespace Fir;

class RoutePart
{
	public $name;
	public $variable;
	public $optional;

	public function __construct(string $name, bool $variable,
		bool $optional)
	{
		$this->name = $name;
		$this->variable = $variable;
		$this->optional = $optional;
	}
}
