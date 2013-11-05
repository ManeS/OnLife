<?php
define( 'DIR_PACKAGE',			dirname(__FILE__).DS );

class PackageOnlife extends \System\Package
{
	public function onLoad()
	{
		
		// Активация кэширования
		$this->cache->Enable(DIR_PACKAGE);
		
		// Получаение аргументов запроса
		$args = $this->request->arguments = $this->detector->getArguments();
		
		/*
		$this->db->init(
			$this->config->driver,
			$this->config->db_host,
			$this->config->db_user,
			$this->config->db_pass,
			$this->config->db_base,
			$this->config->db_encode
		);
		//*/
		
		//qr($this->config);
		
		//qr(DIR_ROOT);
		
		$this->locale->folder = DIR_PACKAGE . 'language' . DS;
		$this->locale->setLanguage('russian');
		$this->locale->add('russian');
		$this->view->folder = DIR_PACKAGE . 'view' . DS;
		
		
		if ( count($args) == 0 )
		{
			$this->load->controller('common/default');
			$this->controller->fireAction('default');
		}
		elseif ( count($args) == 1 )
		{
			$this->load->controller('common/'.$args[0]);
			$this->controller->fireAction('default');
		}
		elseif ( count($args) == 2 )
		{
			$this->load->controller($args[0].'/'.$args[1]);
			$this->controller->fireAction('default');
		}
		elseif ( count($args) > 2 )
		{
			$this->load->controller($args[0].'/'.$args[1]);
			$this->controller->fireAction($args[2]);
		}
	}
}






