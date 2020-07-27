<?php

declare(strict_types=1);

require_once "route.php";
require_once "router.php";

use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{
	private $R;

	public function setUp()
	{
		$this->R = new \Fir\Router();
	}

	public function testEmptyRoute() : void
	{
		$route = new \Fir\Route($this->R, "", function() {}, []);

		$this->assertTrue($route->matches("", "GET"), "Correct usage; empty");
		$this->assertTrue($route->matches("/", "GET"), "Correct usage");
		$this->assertTrue($route->matches("//", "GET"), "Correct usage with extra slash");
		$this->assertFalse($route->matches("/foobar", "GET"), "Incorrect usage");
		$this->assertFalse($route->matches("/foobar/", "GET"), "Incorrect usage with trailing slash");
	}

	public function testSlashRoute() : void
	{
		$route = new \Fir\Route($this->R, "/", function() {}, []);

		$this->assertTrue($route->matches("", "GET"), "Correct usage; empty");
		$this->assertTrue($route->matches("/", "GET"), "Correct usage");
		$this->assertTrue($route->matches("//", "GET"), "Correct usage with extra slash");
		$this->assertFalse($route->matches("/foobar", "GET"), "Incorrect usage");
		$this->assertFalse($route->matches("/foobar/", "GET"), "Incorrect usage with trailing slash");
	}

	public function testSingleItem() : void
	{
		$route = new \Fir\Route($this->R, "/home", function() {}, []);

		$this->assertTrue($route->matches("/home", "GET"), "Correct usage");
		$this->assertTrue($route->matches("/home/", "GET"), "Correct usage with trailing slash");
		$this->assertFalse($route->matches("/foobar", "GET"), "Incorrect usage");
		$this->assertFalse($route->matches("/foobar/", "GET"), "Incorrect usage with trailing slash");
	}

	public function testVariable() : void
	{
		$route = new \Fir\Route($this->R, "/profile/<id>", function() {}, []);

		$this->assertTrue($route->matches("/profile/foobar", "GET"), "Correct usage");
		$this->assertTrue($route->matches("/profile/foobar/", "GET"), "Correct usage with trailing slash");
		$this->assertFalse($route->matches("/profile", "GET"), "Incorrect usage");
		$this->assertFalse($route->matches("/profile/", "GET"), "Incorrect usage with trailing slash");
	}

	public function testOptionalVariable() : void
	{
		$route = new \Fir\Route($this->R, "/<id>?", function() {}, []);

		$this->assertTrue($route->matches("/foobar", "GET"), "Correct usage");
		$this->assertTrue($route->matches("/foobar/", "GET"), "Correct usage with trailing slash");
		$this->assertTrue($route->matches("/", "GET"), "Correct usage without optional");
		$this->assertTrue($route->matches("//", "GET"), "Correct usage without optional with trailing slash");
		$this->assertTrue($route->matches("", "GET"), "Correct usage without; empty");
	}

	public function testOptionalVariableAfterNonOptional() : void
	{
		$route = new \Fir\Route($this->R, "/profile/<id>?", function() {}, []);

		$this->assertTrue($route->matches("/profile/foobar", "GET"), "Correct usage");
		$this->assertTrue($route->matches("/profile/foobar/", "GET"), "Correct usage with trailing slash");
		$this->assertTrue($route->matches("/profile", "GET"), "Correct usage without optional");
		$this->assertTrue($route->matches("/profile/", "GET"), "Correct usage without optional with trailing slash");
		$this->assertFalse($route->matches("/profile/foobar/bar", "GET"), "Extra parameter");
		$this->assertFalse($route->matches("/profile/foobar/bar/", "GET"), "Extra parameter with trailing slash");
	}

	public function testOptionalPart() : void
	{
		$route = new \Fir\Route($this->R, "/foobar?", function() {}, []);

		$this->assertTrue($route->matches("/foobar", "GET"), "Correct usage; optional provided");
		$this->assertTrue($route->matches("/", "GET"), "Correct usage; optional not provided");
		$this->assertTrue($route->matches("", "GET"), "Correct usage; empty");
		$this->assertFalse($route->matches("/barfoo", "GET"), "Incorrect usage");
		$this->assertFalse($route->matches("/barfoo/", "GET"), "Incorrect usage with trailing slash");
	}

	public function testOptionalPartWithNonOptional() : void
	{
		$route = new \Fir\Route($this->R, "/foo?/bar", function() {}, []);

		$this->assertTrue($route->matches("/foo/bar", "GET"), "Correct usage; optional provided");
		$this->assertTrue($route->matches("/bar", "GET"), "Correct usage; optional not provided");
		$this->assertTrue($route->matches("/bar/", "GET"), "Correct usage; optional not provided with trailing slash");
		$this->assertFalse($route->matches("/bar/foo", "GET"), "Incorrect usage");
		$this->assertFalse($route->matches("/barfoo/", "GET"), "Incorrect usage with trailing slash");
	}

	public function testGetParameters() : void
	{
		$route = new \Fir\Route($this->R, "/test/<a>/<b>?", function() {}, []);

		$params_1 = $route->get_parameters("/test/foo/bar");
		$params_2 = $route->get_parameters("/test/baz");

		$this->assertArrayHasKey("a", $params_1, "Check parameter 'a' is present");
		$this->assertArrayHasKey("b", $params_1, "Check parameter 'b' is present");
		$this->assertEquals("foo", $params_1["a"], "Check value of 'a'");
		$this->assertEquals("bar", $params_1["b"], "Check value of 'a'");

		$this->assertArrayHasKey("a", $params_2, "Check parameter 'a' is present");
		$this->assertArrayNotHasKey("b", $params_2, "Check parameter 'b' is not present");
		$this->assertEquals("baz", $params_2["a"], "Check value of 'a'");
	}

	public function testGetParametersNested() : void
	{
		$router_1 = new \Fir\Router("/");
		$router_2 = new \Fir\Router("/r2");

		$route = new \Fir\Route($router_2, "/<id>", function() {}, []);

		$params = $route->get_parameters("/r2/foobar");

		$this->assertArrayHasKey("id", $params, "Check parameter 'id' is present");
		$this->assertEquals("foobar", $params["id"], "Check value of 'id'");
	}

	public function testGetParametersRouter() : void
	{
		$router = new \Fir\Router("/<rt>");
		$route = new \Fir\Route($router, "/foo", function() {}, []);

		$params = $route->get_parameters("/foobar/foo");

		$this->assertArrayHasKey("rt", $params, "Check parameter 'rt' is present");
		$this->assertEquals("foobar", $params["rt"], "Check value of 'rt'");
	}
}
