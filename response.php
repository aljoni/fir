<?php

namespace Fir;

class Response {

	/**
	 * Reponse body.
	 * @var string
	 */
	public $body;

	/**
	 * Reponse code.
	 * @var int
	 */
	public $code;

	/**
	 * Reponse headers.
	 * @var array[string]string
	 */
	public $headers;

	public function __construct() {
		$this->body = "";
		$this->code = 200;
		$this->headers = [];
	}

	/**
	 * Write content to the reponse body.
	 *
	 * If an array is provided it will be encoded as JSON, and the content type
	 * will be updated to 'application/json'; any existing content will be
	 * overwritten.
	 *
	 * @param $content Value to write to body.
	 */
	public function write($content): void {
		if (is_array($content)) {
			$this->headers["Content-Type"] = "application/json";
			$this->body = json_encode($content);
			return;
		}
		$this->body .= $content;
	}

	/**
	 * Sets the value of a response header.
	 *
	 * @param string $key      Name of header to set.
	 * @param string $value    Value to assign to header.
	 * @param bool   $fix_case Whether to correct the case of the provided key.
	 */
	public function head(string $key, string $value,
			bool $fix_case = TRUE): void {
		if ($fix_case) {
			$this->headers[ucwords($key, "-")] = $value;
			return;
		}
		$this->headers[$key] = $value;
	}

}
