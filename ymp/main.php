<PRE>
    <?php
    require_once 'require.php';


    $url_main = 'http://ymp.loc/yandex_first.html';
    $url_spec = 'http://ymp.loc/yandex_spec_note.html';

    //parser

    $parser = new Parser();
    $makeNewProduct = $parser->makeNewProduct($url_main);
    
    echo $makeNewProduct;
    
    //$parser = new ParserYM();

    //$parse_result = $parser->parseProduct($url_main, $url_spec);

    //$ppage = $parser->findByName("ASUS P8H61-M LX3 R2.0 Soc-1155 iH61 DDR3 mATX AC-97 GbLAN VGA BULK");
    //echo "<h3>$ppage</h3>";

    //var_dump($parse_result);



    //$db = new DataBase();
    //$db->connect('mysql://user:q1w2e3r5@78.46.199.205/test_shop');
    //$DATABASE->query('insert into ');
    ?>
</PRE>

