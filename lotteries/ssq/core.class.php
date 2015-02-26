<?php

class Ssq extends Lottery {

	protected function _init()
	{
		$this->_set = array(
			array(
				'range' => array_combine(range(1, 33), range(1, 33)),
				'repeat' => 6
			),
			array(
				'range' => array_combine(range(1, 16), range(1, 16)),
				'repeat' => 1
			)
		);

		$this->_name = 'ssq';
		$this->_code = 'ssq';
	}
}