<?php

abstract class Helper extends \System\EngineBlock
{
	public function __construct( &$registry )
	{
		parent::__construct($registry);
		$this->onLoad();
	}
	
	
	public function onLoad() {}
}



