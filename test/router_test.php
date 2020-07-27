<?php

declare(strict_types=1);

require_once "router.php";

use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase {

  public function testSingleRouter(): void {
    $router = new \Fir\Router("/");
    $router->add("/", function() {}, []);
    $router->add("/foo", function() {}, []);

    $this->assertNotNull($router->resolve("/", "GET"), "Resolve: /");
    $this->assertNotNull($router->resolve("/foo", "GET"), "Resolve: /foo");
  }

  public function testNestedRouters(): void {
    $router_1 = new \Fir\Router("/");
    $router_2 = new \Fir\Router("/r2");
    $router_3 = new \Fir\Router("/r3");
    $router_1->add_router($router_2);
    $router_2->add_router($router_3);

    $router_2->add("/", function() {}, []);
    $router_2->add("/foo", function() {}, []);
    $router_3->add("/", function() {}, []);
    $router_3->add("/bar", function() {}, []);

    $this->assertNull($router_1->resolve("/", "GET"), "Resolve: /");
    $this->assertNull($router_1->resolve("/foo", "GET"), "Resolve: /foo");
    $this->assertNull($router_1->resolve("/bar", "GET"), "Resolve: /bar");
    $this->assertNotNull($router_1->resolve("/r2", "GET"), "Resolve: /r2");
    $this->assertNotNull($router_1->resolve("/r2/foo", "GET"), "Resolve: /r2/foo");
    $this->assertNotNull($router_1->resolve("/r2/r3", "GET"), "Resolve: /r2/r3");
    $this->assertNotNull($router_1->resolve("/r2/r3/bar", "GET"), "Resolve: /r2/r3/bar");
  }

  public function testNestedRoutersEmptyRoute(): void {
    $router_1 = new \Fir\Router("/");
    $router_2 = new \Fir\Router("/");
    $router_1->add_router($router_2);

    $router_1->add("/foo", function() {}, []);
    $router_2->add("/bar", function() {}, []);

    $this->assertNull($router_2->resolve("/foo", "GET"), "Router 2: /foo");
    $this->assertNotNull($router_1->resolve("/foo", "GET"), "Router 1: /foo");
    $this->assertNotNull($router_1->resolve("/bar", "GET"), "Router 1: /bar");
  }

  public function testPreventRecursiveRouters(): void {
    $this->expectException(Exception::class);

    $router_1 = new \Fir\Router("");
    $router_2 = new \Fir\Router("");
    $router_3 = new \Fir\Router("");

    $router_1->add_router($router_2);
    $router_2->add_router($router_3);
    $router_3->add_router($router_1); // This should fail.
  }

}
