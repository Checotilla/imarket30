<?php
require_once 'require.php';
require_once 'dbsimple.php';

class Parser
{
    private $parser;
    
    function __construct()
    {
        $parser = new ParserYM();
    }
    
    public function makeNewProduct($url)
    {
        $parserYM = new ParserYM();
        $parse_result = $parserYM->parseMainPageFromURL($url);

        /****** !!!!!!!!!! D E B U G !!!!!!!! *********/
        //$parse_result = $parserYM->parseSpecificationPageFromURL("http://market.yandex.ru".$parse_result['pages']['specification']);
        $parse_result['specification'] = $parserYM->parseSpecificationPageFromURL('http://ymp.loc/yandex_spec.html');
        
        
        $this->newProductToDB($parse_result);
        var_dump($parse_result);
        
        

        return $parse_result;
    }
    
    private function newProductToDB($product)
    {
        global $DB;
        
        //product type
        $table_name = 'product_type';
        $field_name = 'name';
        $value = $product['product_type'];
        $result = $DB->select('SELECT ?#, ?#  FROM ?# WHERE ?#=?', "id", $field_name, $table_name, $field_name, $value);
        //$rows = $DB->select('SELECT * FROM product_type WHERE name="Материнские платы"', "id", $field_name, $table_name, $field_name, $value);

        
        if (count($result) > 0) 
        {
            $id = $result[0]['id'];
        }
        else //adding new element
        {
            $id = $DB->query('INSERT INTO ?# SET ?#=?', $table_name, $field_name, $value);
        }
         
        //echo "<h2>$id</h2>";
        
        return;
    }
    

    
    public function parseProductByName($product_name)
    {
        
    }
    
    
}



?>
