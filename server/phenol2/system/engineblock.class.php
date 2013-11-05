<?php

namespace System;


/**
 * EngineBlock - используется как основа классов ядра
 * 
 * @package Phenol2.com
 * @author LestaD
 * @copyright 2013
 * @version 1
 * @access public
 */
class EngineBlock
{
	protected $registry;
	
	public function __construct( \Core\Registry &$reg )
	{
		$this->registry = $reg;
	}
	
		
	/**
	 * Магический метод вызывается когда происходит попытка
	 * получения значения не существующей переменной
	 * 
	 * @param mixed $key
	 * @return
	 */
	public function __get( $key )
	{
		return $this->registry->get( $key );
	}
	
		
	/**
	 * Магический метод вызывается когда происходит попытка
	 * установки значение не существующей переменной
	 * 
	 * @param mixed $key
	 * @param mixed $value
	 * @return void
	 */
	public function __set( $key, $value )
	{
		$this->registry->set( $key, $value );
	}
}