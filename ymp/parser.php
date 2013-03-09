<?php
require_once 'require.php';

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
        var_dump($parse_result);
        return $parse_result;
    }
    
    
    public function parseProductByName($product_name)
    {
        
    }
    
    
}



?>
