<?php

namespace System;

class Package extends \System\EngineBlock
{
	/**
	 * Загрузка настроек из файла config.php в папке выбранного пакета
	 * 
	 * @return void
	 */
	public function ConfigLoad()
	{
		include DIR_PACKAGE . 'config.php';
		$this->config->append($config);
		$this->config->usedb = true;
		$this->config->driver = 'mysqli';
		unset($config);
	}
	
}






