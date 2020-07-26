<?php

namespace Fir;

class Router
{
	private const ROUTE_VAR = "/^<[^\[\]]+>$/";

	/**
	 * Base route for all handlers registered with router.
	 * @var string
	 */
	private $route;

	/**
	 * Regex to match first part of path against.
	 * @var string
	 */
	public $regex;

	/**
	 * Parent router.
	 * @var \Fir\Router|null
	 */
	private $parent;

	/**
	 * Stores the routes which are handled by the router.
	 * @var \Fir\Route[] 
	 */
	private $routes;

	/**
	 * Nested routers.
	 * @var \Fir\Route[]
	 */
	private $routers;

	/**
	 * Constructs a new router.
	 *
	 * @param string           $route  Base route of handlers registered to router.
	 * @param \Fir\Router|null $parent Parent router.
	 */
	public function __construct(string $route = "", $parent = NULL)
	{
		$this->route = $route;
		$this->parent = $parent;
		$this->regex = self::route_as_regex($this->get_full_route(), FALSE);
		$this->routes = [];
		$this->routers = [];
	}

	/**
	 * @return string Full route to router.
	 */
	public function get_full_route() : string
	{
		$path = "";
		if ($this->parent !== NULL)
		{
			$path = trim($this->parent->get_full_route(), "/");
		}
		return "/" . trim($path . "/" . trim($this->route, "/"), "/");
	}

	/**
	 * Registers a handler with the router.
	 *
	 * @param string        $route   Route to register.
	 * @param callable      $handler Route handler function.
	 * @param string[]|null $methods Methods accepted by route.
	 */
	public function add(string $route, callable $handler, array $methods) : void
	{
		$this->routes[] = new Route($this, $route, $handler, $methods);
	}

	/**
	 * Add a sub-router.
	 *
	 * @param \Fir\Router $router Router to add.
	 */
	public function add_router($router) : void
	{
		$router->set_parent($this);
		$this->routers[] = new Route($this, $router->route, $router, []);
	}

	/**
	 * Assign parent router.
	 *
	 * @param \Fir\Router $parent Router to set as parent.
	 */
	public function set_parent($parent) : void
	{
		if ($parent === $this)
		{
			throw new \Exception("Parent can't be the child router");
		}

		$this->parent = $parent;
		$this->regex = self::route_as_regex($this->get_full_route(), FALSE);
	}

	/**
	 * Splits the provided route into individual parts, used for path
	 * matching.
	 *
	 * @param string $route Route to parse.
	 *
	 * @return Route parts.
	 */
	public static function route_as_regex(string $route,
		bool $include_end_maker = TRUE) : string
	{
		$route = explode("/", trim($route, " /"));

		$expression = "";
		foreach (array_values($route) as $i => $part)
		{
			$optional = strlen($part) > 0 ? (($part[strlen($part) - 1] === "?") ? "?" : "") : "";
			$part = rtrim($part, "?");

			if (preg_match(self::ROUTE_VAR, $part) === 1)
			{
				$part = trim($part, "<>");
				$expression .= "?(?P<$part>[^\\/]+)$optional";
				$expression .= "\\/$optional";
			}
			else
			{
				$expression .= "($part)$optional";
				$expression .= "\\/$optional";
			}
		}

		$expression = ltrim($expression, "?");
		if ($expression[strlen($expression) - 1] !== "?")
		{
			$expression .= "?";
		}
		if ($include_end_maker)
		{
			$expression .= "$";
		}
		return "/^$expression/";
	}

	/**
	 * Attempts to resolve the provided path into a route.
	 *
	 * @param string $path   Path to resolve.
	 * @param string $method Method to resolve.
	 *
	 * @return callable|null Handler, or null.
	 */
	public function resolve(string $path, string $method)
	{
		foreach ($this->routes as $route)
		{
			if ($route->matches($path, $method))
			{
				return $route->handler;
			}
		}

		foreach ($this->routers as $router)
		{
			if (preg_match($router->handler->regex, $path) === 1)
			{
				continue;
			}
			return $router->handler->resolve($path, $method);
		}

		return NULL;
	}
}
