<?php

require_once 'require.php';
require_once 'dbsimple.php';

class Parser {

    private $parser;

    function __construct() {
        $parser = new ParserYM();
    }

    public function makeNewProduct($url) {
        $parserYM = new ParserYM();
        $parse_result = $parserYM->parseMainPageFromURL($url);

        /*         * **** !!!!!!!!!! D E B U G !!!!!!!! ******** */
//$parse_result = $parserYM->parseSpecificationPageFromURL("http://market.yandex.ru".$parse_result['pages']['specification']);
        $parse_result['specification'] = $parserYM->parseSpecificationPageFromURL('http://ymp.loc/yandex_spec.html');


        $manufacturer = $this->newManufacturerToDB($parse_result['mark']);
        $product_type = $this->newProductTypeToDB($parse_result['product_type']);
        $product = $this->newProductToDB($parse_result['model'], $manufacturer, $product_type);
        $pages = $this->newProductPagesToDB($parse_result['pages'], $product);
        $images = $this->newProductImagesToDB($parse_result['images'], $product);
        $images = $this->newShortSpecificationsToDB($parse_result['short_specification'], $product);



        var_dump($parse_result);



        return $parse_result;
    }

    /*     * *************************************** */

// add manufacturer
    /*     * *************************************** */
    private function newManufacturerToDB($value) {
        global $DB;
        $table_name = 'manufacturer';
        $field_name = 'name';
        $result = $DB->select('SELECT ?#, ?#  FROM ?# WHERE ?#=?', "id", $field_name, $table_name, $field_name, $value);
        if (count($result) > 0) {
            $id = $result[0]['id'];
        } else { //adding new element
            $id = $DB->query('INSERT INTO ?# SET ?#=?', $table_name, $field_name, $value);
        }
        return $id;
    }

    /*     * *************************************** */

// add product_type
    /*     * *************************************** */
    private function newProductTypeToDB($value) {
        global $DB;
        $table_name = 'product_type';
        $field_name = 'name';
        $result = $DB->select('SELECT ?#, ?#  FROM ?# WHERE ?#=?', "id", $field_name, $table_name, $field_name, $value);
        if (count($result) > 0) {
            $id = $result[0]['id'];
        } else { //adding new element
            $id = $DB->query('INSERT INTO ?# SET ?#=?', $table_name, $field_name, $value);
        }
        return $id;
    }

    /*     * *************************************** */

// add product
    /*     * *************************************** */
    private function newProductToDB($value, $manufacturer_id, $product_type_id) {
        global $DB;
        $table_name = 'product';
        $field_name = 'name';
        $result = $DB->select('SELECT ?#, ?#  FROM ?# WHERE ?#=?', "id", $field_name, $table_name, $field_name, $value);
        if (count($result) > 0) {
            $id = $result[0]['id'];
        } else { //adding new element
            $id = $DB->query('INSERT INTO ?#(?#, ?#, ?#) VALUES (?, ?, ?)', $table_name, $field_name, "manufacturer_id", "product_type_id", $value, $manufacturer_id, $product_type_id);
        }
        return $id;
    }

    /*     * *************************************** */

// add product_urls
    /*     * *************************************** */
    private function newProductPagesToDB($values, $product_id) {
        global $DB;
        $table_name = 'product_urls';
        $field_name = 'url';
        foreach ($values as $key => $value) {


            $result = $DB->select('SELECT ?#, ?#  FROM ?# WHERE ?#=?', "id", $field_name, $table_name, $field_name, $value);
            if (count($result) > 0) {
                $id[$key] = $result[0]['id'];
            } else { //adding new element
                $type = 0;
                switch ($key) {
                    case 'main': $type = 0;
                        break;
                    case 'specification': $type = 1;
                        break;
                    case 'prices': $type = 2;
                        break;
                    case 'reviews': $type = 3;
                        break;
                    case 'discussion': $type = 4;
                        break;
                    default : $type = -1;
                }
                $id[$key] = $DB->query('INSERT INTO ?#(?#, ?#, ?#, ?#) VALUES (?, ?, ?, ?)', $table_name, $field_name, "url_type", "url_type_str", "product_id", $value, $type, $key, $product_id);
            }
        }
        return $id;
    }

    /*     * *************************************** */

// add product_images
    /*     * *************************************** */
    private function newProductImagesToDB($values, $product_id) {
        global $DB;
        $table_name = 'product_images';
        $field_name = 'image';
        $is_main = 0;
        foreach ($values as $key => $value) {
            $result = $DB->select('SELECT ?#, ?#  FROM ?# WHERE ?#=?', "id", $field_name, $table_name, $field_name, $value);
            if (count($result) > 0) {
                $id[$key] = $result[0]['id'];
            } else { //adding new element
                if ($key == 0)
                    $weight = 0;
                else
                    $weight = 5;
                $image_path = $this->loadImageFromURL($value);
                $id[$key] = $DB->query('INSERT INTO ?#(?#, ?#, ?#, ?#) VALUES (?, ?, ?, ?)', $table_name, $field_name, "weight", "image_src", "product_id", $image_path, $weight, $value, $product_id);
            }
        }
        return $id;
    }

    
    /*     * *************************************** */

// add product_images
    /*     * *************************************** */
    private function newShortSpecificationsToDB($values, $product_id) {
        global $DB;
        $table_name = 'short_specification';
        $field_name = 'name';
        $is_main = 0;
        foreach ($values as $key => $value) {
            $result = $DB->select('SELECT ?#, ?#  FROM ?# WHERE ?#=?', "id", $field_name, $table_name, $field_name, $value);
            if (count($result) > 0) {
                $id[$key] = $result[0]['id'];
            } else { //adding new element
                $id[$key] = $DB->query('INSERT INTO ?#(?#, ?#, ?#) VALUES (?, ?, ?)', $table_name, $field_name, "num", "product_id", $value, $key, $product_id);
            }
        }
        return $id;
    }
    
    
    private function loadImageFromURL($url) {
        $path = "./data/product_images/";
        $ppos = strrpos($url, '.');
        $ext = substr($url, $ppos);
        $name = $path.md5($url).$ext;
        file_put_contents($name, file_get_contents($url));
        return $name;
    }

    public function parseProductByName($product_name) {
        
    }

}

?>
