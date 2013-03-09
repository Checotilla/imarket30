<?php
//require_once "./lib/config.php";
//require_once "./lib/DbSimple/Generic.php";

require_once "./lib/config.php";
require_once "DbSimple/Generic.php";

//$DB = new DbSimple_Mysql();
// Подключаемся к БД.
//$DB = DbSimple_Generic::connect("mysql://mysqluser:mysqlpassword@localhost/test_shop");
// Подключаемся к БД.
$DB = DbSimple_Generic::connect('mysql://root:@localhost/test_shop');

// Устанавливаем обработчик ошибок.
$DB->setErrorHandler('databaseErrorHandler');

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

$DB->setLogger('myLogger');

function myLogger($db, $sql)
{
  $caller = $db->findLibraryCaller();
  $tip = "at ".@$caller['file'].' line '.@$caller['line'];
  // Печатаем запрос (конечно, Debug_HackerConsole лучше)
  echo "<xmp title=\"$tip\">"; print_r($sql); echo "</xmp>";
}


?>
