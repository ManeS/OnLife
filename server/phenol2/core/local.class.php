<?php

namespace Core;


/**
 * Класс для работы с локализацией сайта
 * 
 * Должен быть подключен класс Ini;
 * 
 * @package Phenol
 * @author LestaD
 * @copyright 2013
 * @version 1.3
 * @access public
 */
class Locale
{
	public $folder;
	
	private $keys;
	
	private $language;
	
	private $filelist;
	
	private $registry;
	
	/**
	 * Конструктор
	 * 
	 * @param mixed $lang - язык локализации
	 * @return
	 */
	public function __construct( &$registry)
	{
		$this->registry = $registry;
		$this->language = 'english';
		$this->keys = array();
		$this->filelist = array();
		$this->folder = '';
	}
	
	
	
	/**
	 * Добавляет файл локализации
	 * 
	 * @param mixed $file - имя файла в папке локализации
	 * @return
	 */
	public function add( $file )
	{
		$fullfile = $this->folder . $this->language . DS . $file . '.' .$this->language. '.inc';
		
		if ( @file_exists($fullfile) )
		{
			$keys = \Toml\Parser2::fromFile($fullfile);
			$this->keys = array_merge($this->keys, $keys);
			$this->filelist[$file] = $file;
		} else {
			$this->registry->error->errorLoadLangFile($file . '.' .$this->language. '.inc', $this->language);
		}
	}
	
	
		
	/**
	 * Locale::addFullPath()
	 * 
	 * @param mixed $file
	 * @return void
	 */
	public function addFullPath( $file )
	{
		if ( @file_exists( $file ) )
		{
			$keys = \Toml\Parser2::fromFile($file);
			$this->keys = array_merge($this->keys, $keys);
			$this->filelist[$file] = $file;
		}
	}
	
	
	
	/**
	 * Установка нового языка
	 * 
	 * @param string $language - язык в формате "english", "russian"
	 * @return void
	 */
	public function setLanguage( $language = "english" )
	{
		$this->language = $language;
		$this->keys = array();
		foreach ( $this->filelist as $file )
		{
			$this->add($file);
		}
	}
	
	
	
	/**
	 * Возвращает название текущего языка локализации
	 * 
	 * @return string
	 */
	public function getLanguage()
	{
		return $this->language;
	}
	
	
		
	/**
	 * Получить список всех языков
	 * 
	 * @return void
	 */
	static public function getAllLanguages()
	{
		$locale = new Locale();
		$list = array();
		
    	return $list;
	}
	
	
	/**
	 * Перевод указанного слова
	 * 
	 * @param mixed $word - слово для перевода
	 * @param string $section - секция поиска (Base)
	 * @return string
	 */
	public function get( $word )
	{
		return isset($this->keys[$word]) ? $this->keys[$word] : NULL;
	}
	
	
	
	/**
	 * Поиск указанного слова в списке локализации
	 * Если слово найдено возвращается его перевод
	 * Если слова не найдено, то возвращается оригинал
	 * 
	 * @param string $word - Слово для перевода
	 * @param string $section - Секция для поиска слова
	 * @return string - перевод слова
	 */
	public function detect( $word, $section = "Base" )
	{
		if (isset($this->keys["_".$word])) { return $this->keys["_".$word]; }
		return isset($this->keys[$word]) ? $this->keys[$word] : $word;
	}
	
	
		
	/**
	 * Является алиасом функции Locale::translate()
	 * Секция поиска указана по умолчанию - Base
	 * 
	 * @param string $word
	 * @return string
	 */
	public function translate( $word )
	{
		return $this->detect( $word );
	}
	
	
	
	/**
	 * Получение списка всех слов текущей локализации
	 * 
	 * @return array
	 */
	public function getAllArray()
	{
		return $this->keys;
	}

}







