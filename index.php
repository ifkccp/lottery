<?php

include_once 'Lottery.class.php';

class A {

	public function __contruct(Lottery $core)
	{
		print_r($core);
	}
}


foreach(glob('./lotteries/*') as $dir)
{
	$name = ucfirst(basename($dir));
	$class_file = $dir . '/core.class.php';
	if(!file_exists($class_file)) continue;

	include $class_file;
	$l = new $name;
	$a = new A($l);
	
	unset($l);
}
