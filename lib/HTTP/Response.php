<?php
namespace Incube\Web\Http;
/** @author incubatio
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
class Response {

	/** @var string */
    private $raw_response = '';

	/** @var string */
    private $headers = '';

	/** @var string */
    private $body = '';

	
	/** @param string $response */
    function __construct($response = '') {
        $this->raw_response = $response;
        list($this->headers, $this->body) = explode("\r\n\r\n", $response);
    }

	/** @return string */
    function get_raw_response() {
        return $this->raw_response;
    }

	/** @return string */
    function get_headers() {
        return $this->headers;
    }

	/** @var bool $decodeJSON 
	  * @return string */
    function get_body($decode_json = false) {
        return $decode_json ? json_decode($this->body, true) : $this->body;
    }
}
?>
