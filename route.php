<?php

namespace Fir;

require_once "router.php";
require_once "route_part.php";

/**
 * Defines the route to an endpoint in your application.
 */
final class Route {

	/**
	 * Parent router to route.
	 * @var \Fir\Router
	 */
	public $parent;

	/**
	 * Route to match against.
	 * @var string
	 */
	public $route;

	/**
	 * Handler for this route, or a router.
	 * @var callable|\Fir\Router
	 */
	public $handler;

	/**
	 * Array of accepted methods.
	 * @var string[]
	 */
	public $methods;

	/**
	 * @param \Fir\Router $parent  Parent to route.
	 * @param string      $route   Route definition.
	 * @param callable    $handler Endpoint handler function.
	 * @param string[]    $methods Array of accepted methods.
	 */
	public function __construct($parent, string $route, $handler,
			array $methods) {
		$this->parent = $parent;
		$this->route = $route;
		$this->handler = $handler;
		$this->methods = $methods;
	}

	public function get_full_route(): string {
		return "/" . trim(
			$this->parent->get_full_route() .
				"/" .
				trim($this->route, "/"),
			"/");
	}

	/**
	 * Determines whether the provided path matches the route.
	 *
	 * @param string $path   Path to check.
	 * @param string $method Request method.
	 *
	 * @return Whether the path matches.
	 */
	public function matches(string $path, string $method): bool {
		$regex = Router::route_as_regex($this->get_full_route());
		$path = "/" . trim($path, " /");
		return preg_match($regex, $path) === 1;
	}

	/**
	 * Get the path parameters from a path.
	 *
	 * @param string $path Path to parse.
	 *
	 * @return array Path parameters.
	 */
	public function get_parameters(string $path): array {
		$regex = Router::route_as_regex($this->get_full_route());

		$matches = [];
		preg_match($regex, $path, $matches);

		foreach ($matches as $k => $v) {
			if (is_int($k)) {
				unset($matches[$k]);
			}
		}
		return $matches;
	}

}
