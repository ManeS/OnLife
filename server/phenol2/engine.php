<?php
define( 'ENGINE',			'Phenol');
define( 'VERSION',			'2.0.0' );

define( 'DS',				'/' );
define( 'DIR_ENGINE',		dirname(__FILE__) . DS );
define( 'DIR_ROOT',			$_SERVER['DOCUMENT_ROOT'] . DS );
define( 'DIR_CORE',			DIR_ENGINE . 'core' . DS );
define( 'DIR_SYSTEM',		DIR_ENGINE . 'system' . DS );
define( 'DIR_LIBRARY',		DIR_ENGINE . 'library' . DS );
define( 'DIR_DRIVER',		DIR_ENGINE . 'driver' . DS );

ini_set('register_globals', 'Off');

include DIR_SYSTEM . 'error.class.php';
include DIR_SYSTEM . 'engineblock.class.php';
include DIR_SYSTEM . 'package.class.php';
include DIR_SYSTEM . 'controller.class.php';
include DIR_SYSTEM . 'model.class.php';
include DIR_SYSTEM . 'helper.class.php';

include DIR_CORE . 'system.php';
include DIR_CORE . 'errorlistener.class.php';
include DIR_CORE . 'registry.class.php';
include DIR_CORE . 'detector.class.php';
include DIR_CORE . 'request.class.php';
include DIR_CORE . 'config.class.php';

include DIR_LIBRARY . 'Toml/Parse.php';

$phenol = new Core\Registry;

// 
$phenol->error = new Core\Phenol2ErrorListener($phenol);

// Все массивы с результатами запроса находятся в этом объекте
$phenol->request = new Core\Request();

// Конфигурация пакета
$phenol->config = new Core\Config();

// Парсинг системных настроек
$phenol->fconfig = (object)\Toml\Parser2::fromFile(DIR_ROOT . 'config.toml');

// Детектор пакетов
$phenol->detector = new Core\Detector($phenol);

// Загрузчик моделей, контроллеров
include DIR_CORE . 'loader.class.php';
$phenol->load = new Core\Loader($phenol);

// Кэширование данных
include DIR_CORE . 'cache.class.php';
$phenol->cache = new Core\Cache($phenol);

// Объект для работы с базой данных
include DIR_CORE . 'db.class.php';
$phenol->db = new Core\Database();

// Обращение к локализации сайтов
include DIR_CORE . 'local.class.php';
$phenol->locale = new Core\Locale($phenol);

// Шаблонизатор
include DIR_CORE . 'view.class.php';
$phenol->view = new Core\View($phenol);






