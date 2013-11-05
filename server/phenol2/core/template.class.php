<?php


/**
 * Template Engine ��� ������ � ���������
 * 
 * ������ �������� � ������������ ��� ��������� ���������� template
 * ������:
 * $tpl = new Template("folder/basedProfile.tpl");
 * ���
 * $tpl = new Template();
 * $tpl->template = "folder/basedProfile.tpl";
 * 
 * 
 * @package Phenol2
 * @author LestaD
 * @copyright 2013
 * @version 0.9
 * @access public
 */
class Template {
	protected $registry;
	
	// ������� ������ � ������
	protected $error = false;
	
	// ������ � ����������� � ����������
	protected $values = array();
	
	// ���������� �������
	protected $code = "";
	
	// ������ �������� ��� �������� � ���������� ��� ����������
	protected $childsv = array();
	
	// ��� �������, ������ ��� �������������
	public $template;
	
	// ������� ����� �� ����� ���������
	public $folder;
	
    // ������� ���� �������� 
	public $locale;
	
	/**
	 * �����������
	 * 
	 * @param string $template
	 * @return
	 */
	public function __construct( &$registry ) {
		$this->registry = $registry;
		$this->folder = '';
		$this->template = '';
		$this->locale = &$this->registry->locale;
	}
	
	
	
	/**
	 * ���������� ���������� ��������
	 * 
	 * @param mixed $var - ���������� � �������
	 * @param mixed $value - �������� ����������
	 * @return
	 */
	public function __set( $var, $value ) {
		if ( $this->error ) return;
		
		if ( $var ) {
			$this->values[$var] = $value;
		}
	}
	public function set($var,$value){$this->__set($var,$value);}
	
		
	/**
	 * ���������� ������� ��� ������� � �������� ������
	 * 
	 * @param mixed $var
	 * @param mixed $tpl
	 * @return
	 */
	public function child( $var, $tpl )
	{
		if ( $this->error ) return;
		
		$tpl = $this->folder . DS .$tpl . '.tpl';
		$code = "";
		
		if ( file_exists( $tpl ) )
		{
			$code = file_get_contents($tpl, false) ?: "[ Error: Template can't be readed! ]";
		}
		else
		{
			//trigger_error("Template \"".$tpl."\" can not be loaded!");
			$code = "[ Error: Template can't be loaded! ]";
		}
		
		$this->childsv[$var] = $code;
	}
	
	
	public function childs( array $childs )
	{
		if ( $this->error ) return;
		foreach( $childs as $key=>$tpl )
		{
			$this->child($key,$tpl);
		}
	}
	
	
		
	/**
	 * �������� ���� ������� ���������������� ��� ������� � �������� ������
	 * 
	 * @param mixed $var
	 * @return
	 */
	public function removeChild( $var )
	{
		if ( $this->error ) return;
		
		unset( $this->childsv[$var] );
	}
	
	
	
	/**
	 * ���������� �������� ���������� �� �������
	 * 
	 * @param mixed $var - ���������� � �������
	 * @return mixed
	 */
	public function __get( $var ) {
		if ( $this->error ) return NULL;
		
		if ( isset( $var ) && $var != NULL && $var != "" ) {
			if ( isset( $this->values[$var] ) ) {
				return $this->values[$var];
			}
		}
		
		return NULL;
	}
	public function get($var){return $this->__get($var);}
	
	
	
		
	/**
	 * ��������� �������� ���������� � ��� ������������� � �����
	 * @param mixed $var
	 * @param mixed $value
	 * @return void
	 */
	public function append( $var, $value )
	{
		$v = $this->get( $var );
		$this->set($var, $v.$value);
	}
	
	
		
	/**
	 * ��������� �������� ���������� � ��� ������������� � ������
	 * 
	 * @param mixed $var
	 * @param mixed $value
	 * @return void
	 */
	public function prepend( $var, $value )
	{
		$v = $this->get( $var );
		$this->set($var, $value.$v);
	}
	
	
	
	/**
	 * ������������� ���������� �������
	 * �� ������ ��� ���� ����������
	 * 
	 * @param mixed $code - HTML ��� �������
	 * @return
	 */
	public function setCode( $code ) {
		if ( $this->error ) return false;
		
		if ( $code != NULL ) {
			$this->template = false;
			$this->code = $code;
		}
	}
	
	
	
	/**
	 * ������ ��� ������ � ������������� ������� � ������������� ����������
	 * 
	 * @param mixed $array
	 * @return
	 */
	public function assign( $array ) {
		if ( isset( $array ) && is_array( $array ) ) {
			foreach ( $array as $var => $value ) {
				$this->values[$var] = $value;
			}
		}
	}
	
	
	public function vars( array $array )
	{
		foreach( $array as $key=>$value )
		{
			$this->set($key,$value);
		}
	}
    
	
	/**
	 * �������� ���������� �� ���������
	 * 
	 * @param mixed $var - ��� ���������� ��� ��������
	 * @return
	 */
	public function remove( $var ) {
		if ( $this->error ) return;
		
		if ( $var ) {
			if ( isset( $this->values[$var] ) ) {
				unset( $this->values[$var] );
			}
		}
	}
	public function __unset($var){$this->remove($var);}
    
	
	
	/**
	 * ������ ������� � ����������
	 * 
	 * @return string - �������� ��� �������
	 */
	protected function ReadTemplate( $tpl )
	{
		if ( $this->template == false && 0 )
		{
			return $this->code;
		}
		
		if ( file_exists( $tpl ) && is_file( $tpl ) )
		{
			$c = file_get_contents($tpl, false);
			$code = ($c !== false) ? $c : "[ Error: Template can't be readed! ]";
			return $code;
		}
		else
		{
			$this->registry->error->errorTemplateRead($tpl);
		}
	}
	
	private function default_vars()
	{
		// ����������� ����������
        $default = array();
        $default['YEAR'] = date("Y");
        $default['MONTH'] = date("m");
        $default['DAY'] = date("d");
        $default['ENGINE'] = ENGINE;
        $default['VERSION'] = VERSION;
        $default['AUTHOR'] = AUTHOR;
        
        // �����������
        return array_merge( $this->locale->getAllArray(), $default );
	}
	
	
	
	/**
	 * ��������� �������
	 * 
	 * @return string - ����������� ��� ��������
	 */
	public function dispatch() {
		if ( $this->error ) return;
		
		$this->assign( $this->default_vars() );
		
		$code = $this->template != false ? $this->ReadTemplate( $this->folder . $this->template . '.tpl' ) : $this->code;
		
		// ������ �������� ��������
		foreach ( $this->childsv as $var => $value )
			$code = str_replace( "{" . $var . "}", $value, $code );
		
		// ������ ���� ���������� � �������� �� � ������
		foreach ( $this->values as $var => $value )
		{
			if ( !is_int( $value ) && !is_float($value) && !is_string($value) ) continue;
			$code = str_replace( "{" . $var . "}", $value, $code );
		}
		
		// �������� ������������ � �������
		$code = preg_replace("/({;.*?})/", "", $code);
		$code = preg_replace("/^(;;.*?\r\n)/m", "", $code);
		
		$sourcecode = "";
		{
			extract( $this->values );
			ob_start();
			
			eval("?>$code<?php\r\n");
			
			$sourcecode = ob_get_contents();
			ob_end_clean();
		}
		return $sourcecode;
	}
	
	
	
	/**
	 * ����� ������� �� ��������
	 * 
	 * @return
	 */
	public function render()
	{
		if ( $this->error ) return;
        
		echo $this->dispatch();
	}
}
