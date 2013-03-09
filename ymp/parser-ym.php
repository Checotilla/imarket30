<?php

class ParserYM {

    private function loadURL_curl($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result_data = curl_exec($ch);
        curl_close($ch);
        return $result_data;
    }

    private function loadURL_file_get_contents($url) {
        $result_data = file_get_contents($url);
        return $result_data;
    }

    private function loadURL($url) {
        return $this->loadURL_file_get_contents($url);
    }

    public function parseProduct($url_main, $url_spec) {
        $result = array();

// DEBUG!!!    
        $main_page = $this->loadURL($url_main);
        $spec_page = $this->loadURL($url_spec);

        //echo $spec_page;
        //mark, model, prices, imgs
        $specification = $this->parseMainPageFromPage($main_page);

        //mark, model, specification
        $specification = $this->parseSpecificationPage($spec_page);


        //echo "<hr>$s - $s1<hr>";
        $result['specification'] = $specification;

        return $result;
    }

    /*****************************************************************/
    // Парсинг страницы характеристик товара. В параметр - URL
    /*****************************************************************/
    public function parseSpecificationPageFromURL($url) {
        $page = $this->loadURL($url);
        return $this->parseSpecificationPage($page);
    }
    
    /*****************************************************************/
    // Парсинг страницы характеристик товара. В параметр - текст страницы
    /*****************************************************************/
    private function parseSpecificationPage($page) {
        $dom = new simple_html_dom();
        $dom->load($page);
        $specification = array();

        //specifications
        $table = $dom->find('table[class=b-properties]');
        if (!$table) {
            error("NO TAG table[class=b-properties]!");
            //error_log("<HR>ERROR! NO TAG TABLE11!<HR>");
            return 0;
        }

        $tr = $table[0]->find('tr');

        $spec_group = '';
        $specs = array();

        foreach ($tr as $tr_) {

            $th = $tr_->find('.b-properties__title');
            if ($th) {
                $specification[$spec_group] = $specs;
                $spec_group = $th[0]->plaintext;
                $specs = array();
            } else {
                $spec_name = $tr_->children(0)->plaintext;
                $spec_value = $tr_->children(1)->plaintext;
                $specs[$spec_name] = $spec_value;
            }
        }
        $specification[$spec_group] = $specs;

        return $specification;
    }

    /*****************************************************************/
    // Парсинг главной страницы товара. В параметр - URL
    /*****************************************************************/
    public function parseMainPageFromURL($url) {
        $page = $this->loadURL($url);
        return $this->parseMainPage($page);
    }
    
    /*****************************************************************/
    // Парсинг главной страницы товара. В параметр - текст страницы
    /*****************************************************************/
    private function parseMainPage($page) {
        $dom = new simple_html_dom();
        $dom->load($page);
        $product = array();

        // PRODUCT TYPE - MARK
        //<div class="b-breadcrumbs"><a class="b-breadcrumbs__link" href="/catalog.xml?hid=91020">Материнские платы</a>&nbsp;/&nbsp;
        //<a class="b-breadcrumbs__link" href="/guru.xmlhid=91020">ASUS</a></div>
        $elements = $dom->find('div[class=b-breadcrumbs]');
        if (!$elements) {
            error("NO TAG div[class=b-breadcrumbs]!");
            return 0;
        }
        $product['product_type'] = $elements[0]->children(0)->plaintext;
        $product['mark'] = $elements[0]->children(1)->plaintext;
        

        //MODEL - <h1>
        $elements = $dom->find('h1');
        if (!$elements) {
            error("NO TAG H1!");
            return 0;
        }
        $h1 = $elements[0]->plaintext;
        $something_else = $elements[0]->children(0)->plaintext;  // например "новинка"
        $product['model'] = substr($h1, strlen($product['mark']) + 1, strlen($h1) - strlen($something_else) - strlen($product['mark']) - 1);

        //PAGES
        //<li class="b-switcher__item"><span class="b-switcher__text"><a class="b-switcher__link" href="/model-spec.xml?CMD=-RR=9,0,0,0-PF=1801946~EQ~sel~18904499-PF=2142398356~EQ~sel~12077975-PF=1801946~EQ~sel~18904499-PF=2142398356~EQ~sel~12077975-VIS=8070-CAT_ID=432460-EXC=1-PG=10&amp;modelid=8341423&amp;hid=91013">Характеристики</a>&nbsp;<span class="b-switcher__cnt"></span></span></li>
        $li_pages = $dom->find('ul li.b-switcher__item');
        if (!$li_pages) {
            error("NO TAG [ul li.b-switcher__item]!");
            return 0;
        }
        $pages = array();
        $pages['main'] =            $li_pages[0]->first_child()->first_child()->href;
        $pages['specification'] =   $li_pages[1]->first_child()->first_child()->href;
        $pages['prices'] =          $li_pages[2]->first_child()->first_child()->href;
        $pages['reviews'] =         $li_pages[3]->first_child()->first_child()->href;
        $pages['discussion'] =      $li_pages[4]->first_child()->first_child()->href;

        //IMAGES
        //<table class="b-model-pictures" id="model-pictures"><tbody><tr><td>
        //<span class="b-model-pictures__big">
        //<a id="id309528" href="http://mdata.yandex.net/i?path=b1204183540_img_id3558108847810653869.jpg" target="_blank">
        //<img src="http://mdata.yandex.net/i?path=b1204183544_img_id1254166399825918010.jpg" alt="ASUS P8H61-M LX3 PLUS R2.0" border="0">
        //<img src="/_c/jwFHpqgylMteMRztI0c1cGQyUT0.png" class="b-model-pictures__zoom" alt="Увеличить" title="Увеличить" border="0" width="16" height="16"></a></span></td><td>
        //<span class="b-model-pictures__small">
        //<a id="id1940176" href="http://mdata.yandex.net/i?path=b1204183544_img_id1041761107040826750.jpg" target="_blank">
        //<img src="http://mdata.yandex.net/i?path=b1204183544_img_id1041761107040826750.jpg&amp;size=1" border="0" title="Увеличить"></a></span></td></tr></tbody></table>
        $pictures = $dom->find('table.b-model-pictures a');
        if (!$pictures) {
            error("NO TAG [table.b-model-pictures a]!");
            return 0;
        }
        $images = array();
        $i = 0;
        foreach($pictures as $a)
        {
            $images[$i++] = $a->href;
        }

        //SHORT SPECIFICATION
        //<ul class="b-vlist b-vlist_type_mdash b-vlist_type_friendly">
        //<li>материнская плата с сокетом LGA1155</li>
        $specs = $dom->find('ul.b-vlist li');
        if (!$specs) {
            error("NO TAG [ul.b-vlist li]!");
            return 0;
        }
        $specifications = array();
        $i = 0;
        foreach($specs as $spec)
        {
            $specifications[$i++] = $spec->plaintext;
        }
        
        //PRODUCT
        $product['pages'] = $pages;
        $product['images'] = $images;
        $product['short_specification'] = $specifications;
        
        return $product;
    }

    /*****************************************************************/
    // поиск товара по имени на маркете. Имя может быть любое.
    /*****************************************************************/
    public function findByName($name) {
        //заменяем все возможные пробельные символы на пробел
        $replace_symbols = array(1=>'\t', 2=>'\r', 3=>'\n', 4=>'\f');
        $name = str_replace($replace_symbols, ' ', $name);

        
        $encode_name = urlencode($name);
        $url = sprintf('http://market.yandex.ru/search.xml?text=%s&cvredirect=2', $encode_name);
        $page = $this->loadURL($url);
        
        $newname = '';
        //отрезаем по части с конца, пока не найдем нужное на яме
        do {
            if (strpos($page, 'b-offers_type_guru') !== false)
                break;

            $delimiters = " \/\\:;";
            $maxpos = 0;
            for ($i = 0; $i < strlen($delimiters); $i++) {
                $newpos = strrpos($name, $delimiters[$i]);
                if ($maxpos < $newpos)
                    $maxpos = $newpos;
            }
            if ($maxpos == 0)
                return false;
            $newname = substr($name, 0, $maxpos);
            $encode_name = urlencode($newname);
            $url = sprintf('http://market.yandex.ru/search.xml?text=%s&cvredirect=2', $encode_name);
            $page = $this->loadURL($url);
        } while (true);

        //собственно парсинг
        $dom = new simple_html_dom();
        $dom->load($page);
        $ym_names = array();

        //div class=b-offers_type_guru
        $divs = $dom->find('div[class=b-offers_type_guru]');
        if (!$divs) {
            echo "<HR>ERROR! NO TAG DIV FROM YM!<HR>";
            return 0;
        }
        
        
//заменяем все возможные разделительные символы на пробел
        $replace_symbols = array(1=>';', 2=>'\\', 3=>'\/', 4=>':');
        $pname = str_replace($replace_symbols, ' ', $newname);
        $aname = explode(' ', $pname);
        
        $names = array();
        $i = 0;
        foreach ($divs as $div)
        {
            $a = $div->find('a[class=b-offers__name]');
            echo '<hr>'.$a[0]->plaintext;
        }
        
        
        
        
        
        $spec_group = '';
        $specs = array();

        foreach ($tr as $tr_) {

            $th = $tr_->find('.b-properties__title');
            if ($th) {
                $specification[$spec_group] = $specs;
                $spec_group = $th[0]->plaintext;
                $specs = array();
            } else {
                $spec_name = $tr_->children(0)->plaintext;
                $spec_value = $tr_->children(1)->plaintext;
                $specs[$spec_name] = $spec_value;
            }
        }
        $specification[$spec_group] = $specs;






        return $result_url;
    }

}

?>
