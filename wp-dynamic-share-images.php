<?php
/*
Plugin Name:  WP Dynamic Share Images
Plugin URI:   https://maxpertici.fr#wp-dynamic-share-images
Description:  /
Version:      0.1
Author:       @maxpertici
Author URI:   https://maxpertici.fr
Contributors:
License:      GPLv2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  wp-dynamic-share-images
Domain Path:  /languages
*/

defined( 'ABSPATH' ) or	die();


function og_image_url ( $url ) {
  
    global $post ;
    wp_dsi_generate_share_image( $post );

    /**
     * 
     * TODO
     * 
     * single only
     * url
     * 
     */

    /**
     * 
     * Yoast wpseo_opengraph_image
     */

    
    return  'http://localhost/wp-org/contrib/wp-content/uploads/wp-dsi/share-image/'.$post->post_name.'.png' ;

    
}
add_filter('wpseo_opengraph_image', 'og_image_url');


/**
 * 
 * 
 * 
 */
function wp_dsi_generate_share_image( $post ){

  

  require_once __DIR__ . '/vendor/autoload.php';

  $mpdf = new \Mpdf\Mpdf();

  /**
   * TODO
   * 
   * vrai content
   * PATHs
   * layout
   */
  $mpdf->WriteHTML('<h1>Hello world!</h1>');

  $mpdf->Output( 'wp-content/uploads/wp-dsi/cache/temp.pdf' );

  $imagick = new Imagick();
  $imagick->readImage('wp-content/uploads/wp-dsi/cache/temp.pdf');
  $imagick->writeImages('wp-content/uploads/wp-dsi/share-image/'.$post->post_name.'.jpg', false);
  
  unlink("wp-content/uploads/wp-dsi/cache/temp.pdf");
}