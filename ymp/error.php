<?php

function error($msg)
{
    $res = "<b>ERROR</b> in <i>".__FILE__."</i> in line: ".__LINE__." Message >> ".$msg;

    
    // Здесь происходит запись в конец файла
    $error_log_filename = "errorlogfile.txt";
    $f = fopen($error_log_filename, 'a');
    fwrite($f, $res . PHP_EOL);
    fclose($f);    
    echo "<hr/>$res<br/>";
    
    return;
}


?>
