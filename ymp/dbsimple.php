<?php
//require_once "./lib/config.php";
//require_once "./lib/DbSimple/Generic.php";

require_once "./lib/config.php";
require_once "DbSimple/Generic.php";

// Подключаемся к БД.
//$DB = DbSimple_Generic::connect("mysql://mysqluser:mysqlpassword@localhost/test_shop");
// Подключаемся к БД.
$DATABASE = DbSimple_Generic::connect('mysql://root:@localhost/test_shop');

// Устанавливаем обработчик ошибок.
$DATABASE->setErrorHandler('databaseErrorHandler');

// Код обработчика ошибок SQL.
function databaseErrorHandler($message, $info)
{
	// Если использовалась @, ничего не делать.
	if (!error_reporting()) return;
	// Выводим подробную информацию об ошибке.
	echo "SQL Error: $message<br><pre>"; 
	print_r($info);
	echo "</pre>";
	exit();
}

?>
