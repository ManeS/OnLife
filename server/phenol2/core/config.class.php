<?php
namespace Core;

class Config
{
	protected $config;
	
	public function __construct( $array = array() )
	{
		$this->config = $array;
	}
	
		
	/**
	 * Добавление массива с записями
	 * 
	 * @param mixed $array
	 * @return void
	 */
	public function append( $array )
	{
		$this->config = array_merge($this->config, $array);
	}
	
		
	/**
	 * Полное обновление данных конфигурации
	 * 
	 * @param mixed $array
	 * @return void
	 */
	public function update( $array )
	{
		$this->config = $array;
	}
	
		
	/**
	 * Магический метод __get
	 * 
	 * @param mixed $key
	 * @return
	 */
	public function __get( $key )
	{
		return $this->config[$key];
	}
	
		
	/**
	 * Магический метод __set
	 * 
	 * @param mixed $key
	 * @param mixed $value
	 * @return void
	 */
	public function __set( $key, $value )
	{
		$this->config[$key] = $value;
	}
}