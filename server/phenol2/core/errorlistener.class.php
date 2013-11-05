<?php

namespace Core;

/**
 * Phenol2ErrorListener
 * 
 * Класс отлавливающий ошибки
 * Возможно будет переписан для поддержки исключений
 * 
 * @package Phenol2.com
 * @author LestaD
 * @copyright 2013
 * @version 1
 * @access public
 */
class Phenol2ErrorListener extends \System\ErrorListener
{
	protected $registry;
	
	public function __construct( &$registry )
	{
		$this->registry = $registry;
	}
	
	public function errorControllerLoad($controller)
	{
		qr('<b>Error:</b> Loading controller: ' . $controller);
		die();
	}
	
	
	public function errorControllerFireAction($controller, $action)
	{
		qr('<b>Error:</b> Fire action <i>' . $action . '</i> in controller: ' . $controller);
		die();
	}
	
	public function errorDriverLoad($driver)
	{
		qr('<b>Error:</b> Loading driver: ' . $driver);
		die();
	}
	
	public function errorPackageLoad($package,$default)
	{
		qr('<b>Error:</b> Loading package: <b>' . $package . '</b> (' . $default . ')' );
		die();
	}
	
	public function errorModelLoad($model)
	{
		qr('<b>Error:</b> Loading model: ' . $model);
		die();
	}
	
	public function errorTemplateRead($tpl)
	{
		qr('<b>Error:</b> Reading template: ' . $tpl);
		die();
	}
	
	public function errorTemplateLoad($tpl)
	{
		qr('<b>Error:</b> Loading template: ' . $tpl);
		die();
	}
	
	
	public function errorFileAccess($file)
	{
		qrd('<b>Error:</b> File not found or access forbidden!');
	}
	
	
	public function errorLoadLangFile($file,$lang)
	{
		qrd('<b>Error:</b> Loading file <u>'.$file.'</u> of language <u>'.$lang.'</u>');
	}
	
	public function errorHelperLoad($helper)
	{
		qrd('<b>Error:</b> Loading helper: ' . $helper);
	}
}


