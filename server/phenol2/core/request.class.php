<?php
namespace Core;

/**
 * Request
 * 
 * @package Phenol2.com
 * @author LestaD
 * @copyright 2013
 * @version 1
 * @access public
 */
final class Request
{
	public $get = array();
	public $post = array();
	public $files = array();
	public $cookie = false;
	public $session = false;
	public $arguments = false;
	
	
	public function __construct()
	{
		session_start();
		$this->get = $_GET;
		$this->post = $_POST;
		$this->files = $_FILES;
		$this->cookie = $_COOKIE;
		$this->session = &$_SESSION;
	}
	
	
}




