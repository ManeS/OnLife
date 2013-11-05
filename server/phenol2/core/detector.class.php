<?php

namespace Core;

/**
 * Detector
 * 
 * Класс для определения пакета загрузки
 * 
 * 
 * @package Phenol2
 * @author LestaD
 * @copyright 2013
 * @version 1.0
 * @access public
 */
final class Detector
{
	private $default_package = 'www';
	private $search_path = '';
	private $load_package = false;
	private $request_uri;
	private $registry;
	private $language = false;
	
	public function __construct( &$registry )
	{
		$this->registry = $registry;
		$this->search_path = DIR_ROOT . 'package_%package%';
		$this->request_uri = $_SERVER['REQUEST_URI'];
		
		$this->language = $this->userLanguage();
		
	}
	
	
	/**
	 * Добавление пути для подключения файлов
	 * 
	 * @param string $packagepath Путь к приложению
	 * @return
	 */
	public function setPackage( $packagename )
	{
		$packagepath = str_replace('%package%', $packagename, $this->search_path);
		if ( file_exists( $packagepath ) && is_dir( $packagepath ) )
		{
			$this->default_package = $packagename;
			return true;
		} else return false;
	}
	
	
	/**
	 * Возвращает текущий полный субдомен
	 * Если запущен по основному домену будет возвращено FALSE
	 * 
	 * @return
	 */
	public function getCurrentSubdomain()
	{
		$host = $_SERVER['HTTP_HOST'];
		$domain = $this->registry->fconfig->Server['Domain'];
		if ( $host == $domain )
		{
			return false;
		}
		else
		{
			$host = str_replace('.'.$domain, '', $host);
			return $host;
		}
		
	}
	
		
	/**
	 * Указание папки поиска пакетов
	 * В пути указать шаблон %package% - имя пакета
	 * 
	 * @param mixed $path
	 * @return void
	 */
	public function searchPackagesIn( $path )
	{
		$this->search_path = $path;
	}
	
		
	/**
	 * !!! УСТАРЕЛО !!!
	 * 
	 * TO REMOVE!
	 * 
	 * @param mixed $domain
	 * @return
	 */
	public function detectDomainPackage( $domain )
	{
		$uri = $_SERVER['HTTP_HOST'];
		$packagename = str_replace('.'.$domain, '', $uri);
		$packagepath = str_replace('%package%', $packagename, $this->search_path);
		$this->load_package = $packagename;
		if ( file_exists( $packagepath ) && is_dir( $packagepath ) )
		{
			return true;
		} else return false;
	}
	
		
	/**
	 * Запуск выбранного пакета
	 * 
	 * TODO: переписать
	 * 
	 * @param bool $package
	 * @return void
	 */
	public function runPackage( $package = false )
	{
		if ( $package !== false ) {
			$packagepath = str_replace('%package%', $package, $this->search_path);
			$defaultpackage = $packagepath;
		} else {
			$packagepath = str_replace('%package%', $this->load_package, $this->search_path);
			$defaultpackage = str_replace('%package%', $this->default_package, $this->search_path);
		}
		$file = '';
		$classname = '';
		if ( file_exists( $packagepath . DS . 'package.php' ) ) {
			$file = $packagepath . DS . 'package.php';
			$classname = $package ?: $this->load_package;
		} elseif ( file_exists( $defaultpackage . DS . 'package.php' )  ) {
			$file = $defaultpackage . DS . 'package.php';
			$classname = $package ?: $this->default_package;
		} else {
			$this->registry->error->errorPackageLoad($package ?: $this->load_package, $package ?: $this->default_package);
		}
		
		include $file;
		$class = createClassname($classname, 'Package');
		$this->registry->package = new $class($this->registry);
		$this->registry->package->ConfigLoad();
		$this->registry->package->onLoad();
	}
	
	
		
	/**
	 * Возвращает список аргументов запроса разделенных / в адресе
	 * 
	 * @param integer $startat
	 * @return
	 */
	public function getArguments( $startat = 0 )
	{
		list($args, $get) = explode('?', $_SERVER['REQUEST_URI']);
		$args = explode('/', $args);
		$arguments = array();
		foreach( $args as $arg )
		{
			if ( $arg == '' || $arg == null ) continue;
			$arguments[] = $arg;
		}
		return $arguments;
	}
	
		
	/**
	 * Был ли запрошен файл
	 * 
	 * @return
	 */
	public function isFileRequested()
	{
		$info = pathinfo($this->request_uri);
		
		return isset( $info['extension'] );
	}
	
	
		
	/**
	 * Попытка отдачи запрошенного файла
	 * 
	 * @return void
	 */
	public function getFileRequested()
	{
		$info = pathinfo($this->request_uri);
		$type = $info['extension'];
		$file = $info['dirname'] . DS . $info['basename'];
		
		// Путь к запущенному пакету
		$package = str_replace('%package%', $this->default_package, $this->search_path);
		
		$mime = '';
		$accept = false;
		// Разрешен ли выбранный тип файла для отдачи браузеру
		if ( isset( $type, $this->registry->fconfig->Files['Accepted'][$type] ) )
		{
			$accept = true;
			$mime = $this->registry->fconfig->Files['Accepted'][$type];
		}
		// Запрещенный тип файлов
		elseif ( isset( $type, $this->registry->fconfig->Files['Forbidden'][$type] ) )
		{
			$accept = false;
			$mime = -1;
		}
		// Выдача браузеру неизвестного системе типа файлов
		else
		{
			$accept = true;
			$mime = $this->registry->fconfig->Files['Default']['default'];
		}
		
		// Проверка наличия файла
		if ( is_file( $package . DS . $file ) && file_exists( $package . DS . $file ) && $accept ) {
			header('Content-Type: ' . $mime, true);
			$code = file_get_contents($package . DS . $file);
			if ( $type == "css" OR $type == "js" ) {
				ob_start();
				eval("?>$code<?php\r\n");
				$ss = ob_get_contents();
				ob_clean();
				echo $ss;
			} else {
				echo $code;
			}
		}
		else
		{
			$this->registry->error->errorFileAccess($file);
		}
	}
	
	
	
	public function userLanguage()
	{
		if ( !$this->language )
		{
			if ( ($list = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE'])) )
			{
				if ( preg_match_all('/([a-z]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/', $list, $list) )
				{
					$this->language = array_combine($list[1], $list[2]);
					foreach( $this->language as $n => $v )
					{
						$this->language[$n] = $v ? $v : 1;
					}
					arsort( $this->language, SORT_NUMERIC );
				}
			}
		}
		
		return $this->language;
	}
	
	
	public function LanguageMatchBest( $aliases, $default = "en" )
	{
		$languages = array();
		foreach ( $aliases as $lang => $alias )
		{
			if ( is_array( $alias ) )
			{
				foreach( $alias as $alias_lang )
				{
					$languages[strtolower($alias_lang)] = strtolower($lang);
				}
			}
			else
			{
				$languages[strtolower($alias)] = strtolower($lang);
			}
		}
		
		foreach ( $this->language as $l => $v )
		{
			$s = strtok($l, '-');
			if ( isset($languages[$s]) ) return $languages[$s];
		}
		
		return $default;
	}
}



