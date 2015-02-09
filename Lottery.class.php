<?php

abstract class Lottery {

	protected $_set = array();
	protected $_name;
	protected $_code;

	protected $_history_file_path;
	protected $_history_data;

	public function __construct()
	{
		$this->_code = strtolower(get_called_class());
		$this->_history_file_path = sprintf("./lotteries/%s/history.json", $this->_code);

		$this->_init();
		$this->_update_history();
		
		if(0 == count($this->_history_data)) return;

		$term = 0;
		if(isset($_GET['term'])) $term = $_GET['term'];
		$balls = $this->_predict($term);
		echo implode(',', $balls);
	}

	abstract protected function _init();

	protected function _update_history()
	{
		$old_data = $this->_get_history_data();
		$new_data = $this->_get_current_data();

		foreach ($new_data as $term => $result)
		{
			if(!isset($old_data[$term]))
				$old_data[$term] = $result;
		}

		krsort($old_data);
		$this->_history_data = $old_data;
		file_put_contents($this->_history_file_path, json_encode($old_data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
	}

	protected function _get_history_data()
	{
		$h_file = $this->_history_file_path;

		if(!file_exists($h_file)) return array();

		$data = json_decode(file_get_contents($h_file), true);
		
		return is_array($data) ? $data : array();
	}

	protected function _get_current_data()
	{
		if(!isset($_GET['update']))
			return array();

		$apiurl = sprintf("http://f.opencai.net/utf8/%s-5.json", $this->_code);
		$data = json_decode(file_get_contents($apiurl), true);

		if(!is_array($data)) return array();

		$ret = array();
		foreach ($data['data'] as $d)
		{
			$ret[$d['expect']] = str_replace('+', ',', $d['opencode']);
		}
		
		ksort($ret);

		return $ret;
	}

	protected function _predict($term = null)
	{
		// check
		$_r = array_values($this->_history_data);
		$balls = explode(',', $_r[0]);

		$set_terms = 0;
		foreach ($this->_set as $v)
		{
			$set_terms += $v['repeat'];
		}

		if(count($balls) != $set_terms)
		{
			echo $this->_name . "号码集合有误！\n";
			return false;
		}
		unset($_r);

		$set = $this->_set;
		foreach ($this->_history_data as $t => $result)
		{
			if($term && $term <= $t) continue;

			$balls = explode(',', $result);
			
			foreach ($balls as $k => $ball)
			{
				$ball = intval($ball);
				if($k < $set[0]['repeat'])
				{
					if(count($set[0]['range']) == $set[0]['repeat']) continue;
					if(isset($set[0]['range'][$ball])) unset($set[0]['range'][$ball]);
				}
				else
				{
					if(count($set[1]['range']) == $set[1]['repeat']) continue;
					if(isset($set[1]['range'][$ball])) unset($set[1]['range'][$ball]);
				}
			}

			if(count($set[0]['range']) == $set[0]['repeat'] && 
				count($set[1]['range']) == $set[1]['repeat']) break;
		}

		return $set[0]['range'] + $set[1]['range'];
	}
}
