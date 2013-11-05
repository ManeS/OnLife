<?php
require 'phenol2/engine.php';

// Поиск пакетов приложений будет производиться в папке
$phenol->detector->searchPackagesIn(dirname(__FILE__).DS.'%package%'.DS);

// Определение текущего домена
if ( $phenol->detector->getCurrentSubdomain() ) {
	// Запуск пакета по имени домена
	$phenol->detector->setPackage($phenol->detector->getCurrentSubdomain());
}
else
{
	// Запуск стандартного пакета
	$phenol->detector->setPackage("onlife");
}


// Если был запрошен файл, а не адрес
if ( $phenol->detector->isFileRequested() ) {
	
	// Запрашиваем вывод файла или системную ошибку
	$phenol->detector->getFileRequested();
	die();
}


// Запуск выбранного пакета
$phenol->detector->runPackage();