<?php
require 'scraperwiki.php';

require 'scraperwiki/simple_html_dom.php';           
set_time_limit( 24 * 3600 );
$dom = new simple_html_dom();
$dom2 = new simple_html_dom();

$proxy = 'http://anonymouse.org/cgi-bin/anon-www.cgi/';
/*

print $html . "\n";
*/
$base_url = "http://boardgamegeek.com";

//scraperwiki::save_var('last_char', ord( 'A' ));  
//scraperwiki::save_var('last_page', 1);  

$char = scraperwiki::get_var('last_char') ;
$last_page = scraperwiki::get_var('last_page') ;


    for( $p = $last_page; $p < 2000; $p++)
    {
        echo "Lettera: " . chr( $char ) . " pagina $p\n";
        scraperwiki::save_var('last_page', $p);
        $url = "http://boardgamegeek.com/browse/boardgame/page/"  . $p ;
        $html = scraperWiki::scrape( $url );
        //usleep(100000);
        $dom->load( $html );
        $items = $dom->find( 'div[id^="results_objectname1"] a');
        foreach( $items as $item )
        {
            $a = $item->href;

            $url2 = $base_url . $a;
            
            $html2 = scraperWiki::scrape( $url2 );
            usleep(100000);
            
            $dom2->load( $html2 );

            $tmp_data = $dom2->find( 'h1.geekitem_title', 0 );
            if( !$tmp_data)
                continue;
            $result['title'] = trim( $tmp_data ->plaintext );

            $image = $dom2->find( 'div#module_2 img', 0 )->src;
            //$image_tag = '<img style="float:left; margin: 10px" src="' . $image . '" />';
            $result['description'] = trim( $dom2->find( 'div#module_4', 0 )->plaintext );
            $result['link'] = $url2;
            $result['image'] = $dom2->find( 'div#module_2 img', 0 )->src;
            //print_r( $result );

            $category = array();
            $tmp_data = $dom2->find( 'a[href^="/boardgamecategory"]' );
            foreach( $tmp_data as $a )
            {
                $category[] = $a->plaintext;
            }
            $result['category'] = serialize( $category );
           
            scraperwiki::save(array('title'), $result);
        }
    }
?>
