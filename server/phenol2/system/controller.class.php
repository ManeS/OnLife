<?php


abstract class Controller extends \System\EngineBlock
{
	public function getName()
	{
		return get_class($this);
	}
	
	
	
	/**
	 * Активация указанного действия для текущего контроллера
	 * 
	 * @param mixed $action
	 * @return void
	 */
	public function fireAction( $action )
	{
		$target = createClassname($action, 'Action');
		if ( is_callable(array(&$this, $target)) )
		{
			call_user_func_array(array(&$this, $target),array());
		}
		else
		{
			$this->registry->error->errorControllerFireAction(get_class($this), $target);
		}
	}
	
	
	
	/**
	 * Перенаправление
	 * ( по моему не сложно догадаться )
	 * 
	 * @param mixed $url
	 * @param integer $status
	 * @return void
	 */
	protected function redirect( $url, $status = 302 )
	{
		header('Status: ' . $status);
		header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $url));
		exit();
	}
	
	
	
	
}


