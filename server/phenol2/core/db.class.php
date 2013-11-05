<?php
namespace Core;

final class Database
{
	private $link;
	public $driver;
	
	public function __construct()
	{
		$this->link = null;
		$this->driver = null;
	}
	
		
	/**
	 * Магический метод __call
	 * Автоматически вызывается на несуществем методе в текущем объекте
	 * 
	 * @param mixed $method
	 * @param mixed $params
	 * @return
	 */
	public function __call($method, $params)
	{
		if ( is_callable(array($this->driver, $method)) == true )
		{
			return call_user_func_array(array(&$this->driver, $method), $params);
		}
		return FALSE;
	}
	
		
	/**
	 * Инициализация подключение драйвера и соединения с базой данных
	 * 
	 * @param mixed $drivername
	 * @param mixed $host
	 * @param mixed $user
	 * @param mixed $pass
	 * @param mixed $dbase
	 * @param bool $encoding
	 * @return void
	 */
	public function init( $drivername, $host, $user, $pass, $dbase, $encoding = false )
	{
		if ( file_exists(DIR_DRIVER . $drivername . '.php') && is_file( DIR_DRIVER . $drivername . '.php' ) )
		{
			include DIR_DRIVER . $drivername . '.php';
			$classname = $drivername;
			$classname{0} = strtoupper($classname{0});
			$classname = 'Driver'.$classname;
			$this->driver = new $classname($host, $user, $pass, $dbase);
			if ( $encoding !== false )
			{
				$this->driver->encoding($encoding);
			}
		}
		else
		{
			global $registry;
			$registry->error->errorDriverLoad($drivername);
		}
	}
	
	
	public function query( $sql )
	{
		$sql = str_replace("(prefix)", $this->config->db_prefix, $sql);
		
		return $this->driver->query($sql);
	}
}






