<?php
namespace Incube\Web\Http;
/** @author incubatio
  * @licence GPLv3.0 http://www.gnu.org/licenses/gpl.html
  */
class Query {

    //protected $_validsMethods = array('CONNECT', 'DELETE', 'GET', 'HEAD', 'OPTIONS', 'POST', 'PUT', 'TRACE');

	/** @var string */
    protected $_http_version = "1.0";

	/** @var string */
    protected $_id;

	/** @var string */
    protected $_host;

	/** @var string */
    protected $_document;

	/** @var string */
    protected $_method;

	/** @var array */
    protected $_data = array();

	/** @var string */
    protected $_data_type = "Text/HTML";

	/** @var string */
    protected $_login;

	/** @var string */
    protected $_password;

    public function __construct() {
    }

	/** @param string $id */
    public function get($id = null) {
        return $this->init_method("GET", $id);
    }

	/** @param string $id */
    public function delete($id = null) {
        return $this->init_method("DELETE", $id);
    }

    /** Send query when id is undefined 
	  * @param array | string $data */
    public function post($data = array()) {
        return $this->init_method("POST", "", $data);
    }

    /** Send a query with a defined id, for creation with _id
      * and update with the specific _id and _rev.
	  * @param string $id
	  * @param array | string $data */
    public function put($id = null, $data = array()) {
        return $this->init_method("PUT", $id = null, $data);
        //must contains _rev for an update
    }

	/** @param string method
	  * @param string $id
	  * @param array | string $data */
    protected function init_method($method, $id = "", $data = array()) {
        $this->_method  = $method;
        $this->_data   = $data;
        $this->setId($id);
        return $this;
    }

	/** @param string $id */
    public function setId($id) {
        $this->_id = $id;
    }
	
	/** @return string */
    public function getId() {
        return $this->_id;
    }

	
	/** @param string $database */
    public function in($database) {
        $this->_db = $database;
        return $this;
    }


	/** @param string $host */
    public function to($host) {
        $this->_host = $host;
        return $this;
    }

	/** @param string $data_type */
    public function set_data_type($data_type) {
        $this->_data_type   = $data_type;
        //TODO : check datatype, regroup usage contentType and datatypes i don't know how, maybe in a structure/static class
        return $this;
    }

	/** @param string $login
	  * @param string $password */
    public function logAs($login = null, $password = null) {
        $this->_login       = $login;
        $this->_password    = $password;
        return $this;
    }

    public function get_query() {
		// Init URI
        $uri[] = "";
        $uri[] = $this->_db;
        if(!empty($this->_id)) {
            $uri[] = $this->_id;
        }
        $uri = implode("/", $uri);
        $data = json_encode($this->_data);
//        $elements = array();
//        foreach($this->_data as $key => $value) {
//            $elements[] = "\"$key\" : \"$value\"";
//        }
//        $data = implode("'", $elements);

        $query[] = "{$this->_method} {$uri} HTTP/" . $this->_http_version;
        $query[] = "Host: {$this->_host}";

        if($this->_login || $this->_password) {
            $query[] .= 'Authorization: Basic '.base64_encode($this->_login.':'.$this->_password);
        }

        if($this->_data || $this->_data_type) {
            $query[] .= 'Content-Length: ' . strlen($data);
            //application/json
            $query[] .= "Content-Type: $this->_data_type \r\n";
            $query[] .= $data;
        } else {
            $query[] = "";
        }
        $test=implode("\r\n", $query);
//        Incube_Debug::dump($test);
        return implode("\r\n", $query);
    }

}

?>
