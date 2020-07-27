<?php

namespace Fir;

/**
 * Stores information about request.
 */
final class Request {

  /**
   * URL parameters of request.
   * @var array[string]string
   */
  public $params;

  /**
   * Gets the value of a URL paramater, or returns the default value if none
   * was provided.
   *
   * @param string $key     Name of URL paramater.
   * @param srring $default Default value to return if paramater was not
   *                        present.
   *
   * @return URL paramater, or default value.
   */
  public function param(string $key, string $default = NULL) {
    if (array_key_exists($key, $this->params)) {
      return $this->params[$key];
    }
    return $default;
  }

}
