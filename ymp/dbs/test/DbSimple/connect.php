<?php ## ����������� � ��.
require_once "../../lib/config.php";
require_once "DbSimple/Generic.php";

// ������������ � ��.
$DATABASE = DbSimple_Generic::connect('mysql://root:@localhost/test_shop');

// ������������� ���������� ������.
$DATABASE->setErrorHandler('databaseErrorHandler');

// ��� ����������� ������ SQL.
function databaseErrorHandler($message, $info)
{
	// ���� �������������� @, ������ �� ������.
	if (!error_reporting()) return;
	// ������� ��������� ���������� �� ������.
	echo "SQL Error: $message<br><pre>"; 
	print_r($info);
	echo "</pre>";
	exit();
}
?>
