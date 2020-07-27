<?php

namespace Fir;

require_once "request.php";
require_once "response.php";
require_once "router.php";

class Application extends Router {

	/**
	 * Run the application to handle incomming request.
	 *
	 * @param string $path   Requested path.
	 * @param string $method Request method.
	 */
	public function run(string $path = NULL, string $method = NULL) {
		if ($path === NULL) {
			if (isset($_GET["__path"])) {
				$path = $_GET["__path"];
			} else {
				$path = "/";
			}
		}
		if ($method === NULL) {
			$method = $_SERVER["REQUEST_METHOD"];
		}

		$route = $this->resolve($path, $method);

		$req = new Request();
		$res = new Response();

		if ($route === NULL) {
			$res->code = 404;
			$res->write("<title>404: Not Found</title>");
			$res->write("<center>");
			$res->write("<h1>404: Not Found</h1>");
			$res->write("<hr/>");
			$res->write("<p>The page that you requested was not found on this server.</p>");
			$res->write("</center>");
		} else {
			$req->params = $route->get_parameters($path);
			($route->handler)($req, $res);
		}

		foreach ($res->headers as $k => $v) {
			header("$k: $v");
		}
		print($res->body);
		http_response_code($res->code);
	}

}
