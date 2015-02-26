<?php

class Dlt extends Lottery {

	protected function _init()
	{
		$this->_set = array(
			array(
				'range' => array_combine(range(1, 35), range(1, 35)),
				'repeat' => 5
			),
			array(
				'range' => array_combine(range(1, 12), range(1, 12)),
				'repeat' => 2
			)
		);

		$this->_name = 'dlt';
		$this->_code = 'dlt';
	}
}