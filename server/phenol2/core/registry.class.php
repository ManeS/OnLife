<?php
/**
 * @package Core
*/

namespace Core;


/**
 * Управление картой значений
 * 
 * @package Phenol2
 * @author LestaD
 * @copyright Liberty 2013
 * @version 1.0.0
 * @access public
 */
final class Registry
{
	/**
	 * @var array Массив с данными
	*/
	private $data = array();
		
	/**
	 * Установка значения в карту
	 * 
	 * @param string $key Ключ
	 * @param mixed $value Значение
	 * @return void
	 */
	public function set( $key, $value )
	{
		$this->data[$key] = $value;
	}
	
	public function __set( $key, $value) { return $this->set($key,$value); }
		
	/**
	 * Получение значения
	 * 
	 * В случае отсутствие значения вернет FALSE
	 * 
	 * 
	 * @param string $key Ключ
	 * @return mixed Значение из карты
	 */
	public function get( $key )
	{
		return isset( $this->data[$key] ) ? $this->data[$key] : FALSE;  
	}
	
	public function __get($key) { return $this->get($key); }
		
	/**
	 * Проверка на наличие значения по ключу
	 * 
	 * @param string $key Ключ
	 * @return bool Результат проверки
	 */
	public function has( $key )
	{
		return isset( $this->data[$key] ) ? TRUE : FALSE;
	}
}



