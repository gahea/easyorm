<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
Product Name: Codeigniter easy ORM
SOURCE URI: https://github.com/gahea/easyorm
Author: augustus (freeas@gmail.com)
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

*/

class MY_Model extends CI_Model{

	function __construct(){
		parent::__construct();
	}

	public function initialize($values = array(), $dec = array()){

		if(is_array($values)){

			foreach ($values as $key => $value) {

				if(substr($key,0,1) == '_'){
					continue;
				}

				if (is_array($dec) && count($dec) > 0) {
					if (in_array($key, $dec)) {
						continue;
					}

				}

				if(is_string($value) && $this->_isJson($value)){
					$value = json_decode($value, true);
				}

				if (property_exists(get_class($this), $key)) {
					$this->{$key} = $value;
				}

			}

		}

	}

	public function save(){

		foreach (get_object_vars($this) as $key => $value) {

			if ($key == $this->_getKey()) {
				continue;
			}

			if(substr($key,0,1) == '_'){
				continue;
			}

			if (is_string($value) && substr($value, -2) == '()') {
				$this->db->set($key, $value, false);
				continue;
			}

			if (is_array($value)){
				$value = json_encode($value);
			}

			$this->db->set($key, $value);

		}

		if(method_exists($this, 'prepersist')){
			$this->prepersist();
		}

		$this->db->insert($this->_table);

		$this->{$this->_getKey()} = $this->db->insert_id();

		return $this;

	}

	public function update($inc = array(), $dec = array()){

		if (empty($this->{$this->_getKey()})) {
			throw new Exception("do get before update");
		}

		foreach (get_object_vars($this) as $key => $value) {

			if(substr($key,0,1) == '_'){
				continue;

			}

			if ($key == $this->_getKey()) {
				continue;

			}

			if (is_array($inc) && count($inc) > 0) {
				if (!in_array($key, $inc)) {
					continue;
				}

			}

			if (is_array($dec) && count($dec) > 0) {
				if (in_array($key, $dec)) {
					continue;
				}

			}

			if (is_string($value) && substr($value, -2) == '()') {
				$this->db->set($key, $value, false);
				continue;
			}

			if (is_array($value)){
				$value = json_encode($value);
			}

			$this->db->set($key, $value);

		}

		$this->db->where($this->_getKey(), $this->{$this->_getKey()});

		if(method_exists($this, 'preupdate')){
			$this->preupdate();
		}

		$this->db->update($this->_getTable());


	}

	public function delete($keyValue = 0){

		if (empty($keyValue) || $keyValue == 0) {
			$keyValue = $this->{$this->_getKey()};
		}

		if (empty($keyValue)){
			throw new Exception("entity key can't be null");
		}

		$this->db->where($this->_getKey(), $keyValue);

		if(method_exists($this, 'predelete')){
			$this->predelete();
		}

		$this->db->delete($this->_getTable());

	}

	public function get($keyValue){

		if (empty($keyValue)) {
			$keyValue = $this->{$this->_getKey()};
		}

		if (empty($keyValue)){
			throw new Exception("entity key can't be null");
		}

		$this->db->where($this->_getKey(), $keyValue);
		$query = $this->db->get($this->_getTable());

		if($query->num_rows() == 0){
			show_404();
		}

		$result = $query->row_array();

		$this->initialize($result);

		return $this;

	}

	private function _getKey(){

		$id = $this->_key;

		if(empty($id)){
			$id = 'id';
		}

		return $id;

	}

	private function _getTable(){

		$table = $this->_table;

		if(empty($table)){
			$table = get_class($this);
		}

		return $this->_prefix.$table;
	}

	private function _isJson($string){
		if(!is_string($string) || is_numeric($string)){
			return false;
		}
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}

}
