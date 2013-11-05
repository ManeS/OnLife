<?php

namespace Core;


class Cache
{
	private $tLong;
	private $tDefault;
	private $tQuick;
	private $Folder;
	private $enabled = false;
	private $Name;
	
	private $registry;
	
	public function __construct( &$registry )
	{
		$this->registry = $registry;
		
		$this->tLong		= isset($this->registry->fconfig->Cache['Long'])		? $this->registry->fconfig->Cache['Long']		: 1200;
		$this->tDefault		= isset($this->registry->fconfig->Cache['Default'])	? $this->registry->fconfig->Cache['Default']	: 120;
		$this->tQuick		= isset($this->registry->fconfig->Cache['Quick'])		? $this->registry->fconfig->Cache['Quick']	: 10;
		$this->Name			= isset($this->registry->fconfig->Cache['Folder'])	? $this->registry->fconfig->Cache['Folder']	: "cached";
	}
	
		
	/**
	 * Включает поддержку кеширования в работающем пакете
	 * 
	 * @param mixed $packageFolder
	 * @return void
	 */
	public function Enable( $packageFolder )
	{
		$folder = $packageFolder . DS . $this->Name;
		if ( !file_exists( $folder ) || !is_dir( $folder . DS ) )
		{
			mkdir( $folder . DS );
		}
		$this->Folder = $folder;
		$this->enabled = true;
	}
	
		
	/**
	 * Отключение кеширования
	 * 
	 * @return void
	 */
	public function Disable()
	{
		$this->enabled = false;
	}
	
	
		
	/**
	 * Получение кэша
	 * 
	 * @param mixed $key
	 * @param mixed $expire
	 * @return
	 */
	public function Get( $key, $expire )
	{
		$p = $key;//md5($key);
		$filen = $this->Folder . DS . $p . '.cache';
		if( file_exists( $filen ) )
		{
			if ( (time() - $expire) < filemtime($filen) )
			{
				return unserialize(file_get_contents( $filen ));
			}
		}
		return FALSE;
	}
	
		
	/**
	 * Запись кэша
	 * 
	 * @param mixed $key
	 * @param mixed $value
	 * @return
	 */
	public function Write( $key, $value )
	{
		$p = $key;//md5($key);
		$filen = $this->Folder . DS . $p . '.cache';
		@unlink($filen);
		file_put_contents( $filen, serialize($value) );
		return true;
	}
	
	
	
	public function Long( $key, $value = NULL )
	{
		if ( $value === NULL )
		{
			return $this->Get($key, $this->tLong);
		}
		else
		{
			return $this->Write($key, $value);
		}
	}
	
	
	public function Cache( $key, $value = NULL )
	{
		if ( $value === NULL )
		{
			return $this->Get($key, $this->tDefault);
		}
		else
		{
			return $this->Write($key, $value);
		}
	}
	
	public function Quick( $key, $value = NULL )
	{
		if ( $value === NULL )
		{
			return $this->Get($key, $this->tQuick);
		}
		else
		{
			return $this->Write($key, $value);
		}
	}
	
	
}


