<?php

namespace Fir;

/**
 * Stores data for a part of a route.
 *
 * @see \Fir\Route
 */
class RoutePart {

	/**
	 * Name of the part; will be matched against, or used as the parameter name.
	 * @var string
	 */
	public $name;

	/**
	 * Whether the part is a parameter.
	 * @var bool
	 */
	public $variable;

	/**
	 * Whether the part is optional.
	 * @var bool
	 */
	public $optional;

	/**
	 * @param string $name     Name of the part; will be matched against, or
	 *                         used as the parameter name.
	 * @param string $variable Whether the part is a parameter.
	 * @param string $optional Whether the part is optional.
	 */
	public function __construct(string $name, bool $variable,
			bool $optional) {
		$this->name = $name;
		$this->variable = $variable;
		$this->optional = $optional;
	}

}
