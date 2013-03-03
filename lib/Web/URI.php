<?php
namespace Incube\Web;
/** @author incubatio 
  * @depandancy Incube_Pattern_IURI 
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  *
  * TOTHINK: this class name is a URI parser, not a request object URI_Parser
  */
use Incube\Pattern\IURI;
class URI implements IURI {

    /** @var string */
    protected $_scheme = /*http://myWebsite.com/(lang/)*/'controller/action'/*/id/1*/;

    /** @var string */
    protected $_separator = "/";

    /** @var string */
    protected $_variable_delimiter = ":";

    /** @var array */
    protected $_params;

    /** @var array */
    protected $_main_params;

    /** @var array */
    protected $_unplanned_params;

    /** @var string */
    protected $_indexFile = 'index.php';

    /** @var string */
    protected $_headers;

	/** @param string $URI
	  * @param array options */
    public function __construct($URI, array $options = array()) {

         $this->init_options($options);

         $params = explode($this->_separator, $URI);

        $this->_params = $this->_parse_params($params);
    }

	/** @param array options */
    public function init_options(array $options) {
        foreach ($options as $key => $option) {
            $this->{"_$key"} = $option;
        }
    }

	/** @param strin $key
	  * @return mixed */
	public function __get($key) {
		switch(true) {
			case isset($_SERVER[$key]):
			return $_SERVER[$key];
			case isset($_ENV[$key]):
			return $_ENV[$key];
			default:
			return null;
		}
	}


	/** TODO: REVIEW get_content_type Incube_Router_Request method
	  * No priority, just check if Http request allow content-type
    * TODO: ContentType belonf to a Request object not to a URI
	  * @return mixed  */
	public function get_content_type() {
		if ($this->HTTP_ACCEPT) {
			$mediaTypes = explode(",", $this->HTTP_ACCEPT);
			$type = array();
			foreach ($mediaTypes as $mediaType) {
				$tmp = explode(";", $mediaType);
				$tmp = explode("/", $tmp[0]);
				$type[] = $tmp[1];
			}
			return $type;
		}   
		return false;
	}

    /** @param array $params
      * @return array */
    protected function _parse_params($params) {
        //Get main params from the $URI thanks to the _scheme
        $params = array_values($params);
        $main_params = array();
        foreach(explode($this->_separator, $this->_scheme) as $key => $value) {
            if(empty($value)) continue;
			if(substr($value, 0, 1) === $this->_variable_delimiter) {
				$value = substr($value, 1);
				if(empty($params[$key])){
					if(!array_key_exists($value, $this->_default)) throw new Incube_Exception("Dynamic Params:$value is missing");
					else $main_params[$value] = $this->_default[$value];
				} else {
					if(isset($this->_validation[$value]) AND !preg_match("/" . $this->_validation[$value] . "/", $params[$key])) 
						throw new Incube_Exception("Wrong URL format");
					$main_params[$value] = $params[$key];
				}
			} else {
				if(!empty($params[$key])){
					if($params[$key] != $value) throw new Incube_Exception("Static Params: $key is missing");
				}
				$main_params[$value] = $value;
			}
			unset($params[$key]);
        }
		$this->_main_params = $main_params;

        //Get the other params form the $URI like : /id/1 makes $param['id'] = 1
        //$other_params is stocked by the class to be reused by a router if necessary
        $this->_unplanned_params = $params;
        $other_params = array();
        foreach($params as $key => $param) {
            if(empty($k)) {
                $k = $param;
            } else {
                $other_params[$k] = $param;
                unset($k);
            }
            unset($params[$key]);
        }
        return array_merge($other_params, $main_params);
    }

	/** @return array */
	public function get_main_params() {
		return $this->_main_params;
	}

    /** @return array */
    public function get_params() {
        return $this->_params;
    }

    /** @param string $key
      * @return string | false */
    public function get_param($key) {
        return array_key_exists($key, $this->_params) ? $this->_params[$key] : false;
    }

    /** @return string */
    public function get_website_base_url() {
        return 'http://' . $_SERVER["SERVER_NAME"];
    }

    /** @return string */
    public function get_scheme() {
        return $this->_scheme;
    }
}
