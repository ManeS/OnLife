<?php

namespace Core;


/**
 * Loader
 * 
 * Загрузчик ресурсов
 * 
 * @package Phenol2.com
 * @author LestaD
 * @copyright 2013
 * @version 1
 * @access public
 */
final class Loader
{
	private $registry;
	
	public function __construct( &$registry )
	{
		$this->registry = $registry;
		$this->registry->set("model", new \stdClass);
		$this->registry->set("helper", new \stdClass);
	}
	
	
	/**
	 * Загрузка контроллера
	 * 
	 * @param mixed $path
	 * @return
	 */
	public function controller( $path )
	{
		$fullpath = DIR_PACKAGE . 'controller' . DS . $path . '.c.php';
		
		if ( @file_exists($fullpath) && is_file($fullpath) )
		{
			$classname = createClassname($path, 'C');
			include $fullpath;
			if ( class_exists($classname) )
			{
				$this->registry->controller = new $classname($this->registry);
				return true;
			}
		}
		
		$this->registry->error->errorControllerLoad($path);
	}
	
		
	/**
	 * Подгрузка модели
	 * 
	 * @param mixed $path
	 * @return void
	 */
	public function model( $path )
	{
		$fullpath = DIR_PACKAGE . 'model' . DS . $path . '.m.php';
		if ( @file_exists($fullpath) && is_file($fullpath) )
		{
			$classname = createClassname($path, 'Model');
			$path = str_replace(' ', '!', $path);
			$path = str_replace('_', '!', $path);
			$path = str_replace('/', '!', $path);
			$path = str_replace('\\', '!', $path);
			$path = str_replace('!', '_', $path);
			include $fullpath;
			$this->registry->model->{$path} = new $classname($this->registry);//set($path, new $classname($this->registry));
		}
		else
		{
			$this->registry->error->errorModelLoad($path);
		}
	}
	
	public function helper( $path )
	{
		$fullpath = DIR_PACKAGE . 'helper' . DS . $path . '.h.php';
		if ( @file_exists($fullpath) && is_file($fullpath) )
		{
			$classname = createClassname($path, 'Helper');
			$path = str_replace(' ', '!', $path);
			$path = str_replace('_', '!', $path);
			$path = str_replace('/', '!', $path);
			$path = str_replace('\\', '!', $path);
			$path = str_replace('!', '_', $path);
			include $fullpath;
			$this->registry->helper->{$path} = new $classname($this->registry);
		}
		else
		{
			$this->registry->error->errorHelperLoad($path);
		}
	}
	
	public function language( $path )
	{
		$this->registry->locale->add($path);
	}
	
	public function errorListener( $classname )
	{
		if( class_exists( $classname ) )
		{
			$this->registry->error = new $classname($this->registry);
		}
	}
}





