<?php
/*
Plugin Name:  Dynamic Share Images
Plugin URI:   https://maxpertici.fr#dynamic-share-images
Description:  /
Version:      0.1
Author:       @maxpertici
Author URI:   https://maxpertici.fr
Contributors:
License:      GPLv2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  dynamic-share-images
Domain Path:  /languages
*/

defined( 'ABSPATH' ) or	die();

/**
 * ! idées à dév.
 * - - - - - - - -
 * il faut que la genration ud pdf et la generation de  l image se fasse en 2 temps.
 * Ca porrait être une options. gen et save 2 fichier sur un chargement, ca me 
 * parait pas tres scalable, du moins si on divise par 2, c'ets plus simple.
 * 
 * Mettre un hook pour setup l option avant d avoir une page de settiongs par exemple
 * 
 */

 /**
  *  Test config serveur on launch the init si OK
  */

function dsimages_launch() {
  
  // TODO : support Gmagick
  // if( ! class_exists('Gmagick') ){ return false ; } 

  if( ! class_exists('Imagick') ){ return false ; }

  require_once __DIR__ . '/vendor/autoload.php';
  require_once __DIR__ . '/inc/helpers.php' ;
  
  add_action( 'template_include', 'dsimages_init' );
  
}

add_action( 'plugins_loaded', 'dsimages_launch' );



/**
 * Init :
 * - - - 
 * depends from dsimages_launch() above
 * 
 */

function dsimages_init( $template ){

  /**
   * TODO : improve early retrun condition
   * - - - - - - - - - - - - - - - - - - - - 
   * pour permettre de déployer progressivemtn la geenration dimage par exemple.
   * avec une fonction de callback filtrable
   * 
   */

  
  $supported_post_types = apply_filters( 'dynamic_share_images/post_types' , [ 'post' ] ) ;
  
  if( ! in_array( get_post_type() , $supported_post_types ) ){ return $template ; }

  $need_share_image = false ;
  foreach( $supported_post_types as $post_type ){
    if( is_singular( $post_type ) ){ $need_share_image = true ; break ; }
  }

  if( false === $need_share_image ){ return $template; }

  // A ce stage, on un single avec un cpt inclus dans les postypes supportes
  // — - - — - -

  // On vérifie si on l image déjà existante, si non, on l a crée.

  global $post ;

  if( false === dsimages_has_share_image( $post ) ){
    dsimages_generate_share_image( $post );
  }
  
  return $template; 

}





/**
 * TODO :
 * - - - -
 * 
 * output le markup og minimal
 * @source : https://ogp.me/
 * 
 */
function dsimages_og_base_markup(){

}

add_filter( 'wp_head', 'dsimages_og_base_markup' );




/**
 * TODO ::
 * 
 * ! le hook est joué que si il y a une images
 * ! il y a aussi des <> og: pour les dimensions de l image
 * 
 * ? custom hook
 * 
 */

function dsimages_og_image_url ( $url ) {
  
    if( ! is_singular('post') ){  return $url ; }

    // TODO : Check if image exist !!
    
    global $post ;
    return  dsimages_get_media_folder_url() . $post->ID . '-' . $post->post_name . '.png' ;
}

add_filter( 'wpseo_opengraph_image', 'dsimages_og_image_url' );





/**
 * Gen. share image for the $post in param
 * @param $post WP_Post 
 * 
 * this function use dompdf
 * @source : https://github.com/dompdf/dompdf
 */

function dsimages_generate_share_image( $post ){
  
  /**
   * TODO
   * true content && php template && layout
   * 
   */

  $dompdf = new \Dompdf\Dompdf();
  
  ob_start();
  include( apply_filters( 'dynamic-share-images/template-path',  __DIR__ . '/inc/image-template.php' ) );
  $template_html = ob_get_contents();
  ob_end_clean();

  $dompdf->loadHtml( $template_html );
  $dompdf->render();

  $id = wp_unique_id() ;

  $output = $dompdf->output();
  file_put_contents( dsimages_get_media_folder_path() . '/temp/temp-'.$id.'.pdf', $output );

  $imagick = new Imagick();
  $imagick->readImage( dsimages_get_media_folder_path() . '/temp/temp-'.$id.'.pdf' );
  $imagick->writeImages( dsimages_get_media_folder_path() . '/cache/' . $post->ID . '-' .$post->post_name.'.png', false );

  unlink( dsimages_get_media_folder_path() . '/temp/temp-'.$id.'.pdf' );

}