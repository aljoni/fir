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
}
