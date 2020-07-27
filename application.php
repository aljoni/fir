<?php

namespace Fir;

require_once "router.php";

class Application extends Router
{
	public function run(string $path = "-", string $method = "-")
	{
		if ($path === "-")
		{
			if (isset($_GET["__path"]))
			{
				$path = $_GET["__path"];
			}
			else
			{
				$path = "/";
			}
		}

		if ($method === "-")
		{
			$method = $_SERVER["REQUEST_METHOD"];
		}

		$route = $this->resolve($path, $method);
		if ($route === NULL)
		{
			echo "404!";
			return;
		}

		$parameters = $route->get_parameters($path);
		if ($route instanceof \Fir\Router)
		{
			// TODO: Handle resolution to Router.
			return;
		}
		($route->handler)($parameters);
	}
}
