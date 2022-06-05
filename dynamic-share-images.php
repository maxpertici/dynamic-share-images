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
 * 
 * 
 * 
 */

function dsimages_prep_images( $template ){

  // TODO : improve early retrun condition
  // pour permettre de déployer progressivemtn la geenration dimage par exemple.

  if( ! is_singular('post') ){  return $template ; }

  global $post ;

  if( false === dsimages_has_share_image( $post ) ){
    dsimages_generate_share_image( $post );
  }
  
  return $template; 

}

add_action( 'template_include', 'dsimages_prep_images' );




/**
 * ! le hook est joué que si il y a une images
 * ! il y a aussi des og pour les dimensiosn de l image
 * 
 * ? custom hook
 * 
 */

function dsimages_og_image_url ( $url ) {
  
    if( ! is_singular('post') ){  return $url ; }

    // check if image exist
    global $post ;
    return  dsimages_get_media_folder_url() . $post->ID . '-' . $post->post_name . '.png' ;
}

add_filter( 'wpseo_opengraph_image', 'dsimages_og_image_url' );





/**
 * 
 * 
 */
function dsimages_get_media_folder_url(){

  if( dsimages_has_uploads_folder() ){

    $wp_upload_dir = wp_get_upload_dir() ;
    return  $wp_upload_dir['baseurl'] . '/dynamic-share-images/cache/' ;

  }

  return false ; 
}



/**
 * 
 * 
 */

 function dsimages_get_media_folder_path(){

    if( dsimages_has_uploads_folder() ){
      $wp_upload_dir = wp_get_upload_dir() ;
      return  $wp_upload_dir['basedir'] . '/dynamic-share-images/' ;
    }

    return false ; 

 }




/**
 * 
 * 
 */
function dsimages_has_uploads_folder(){

  // https://developer.wordpress.org/reference/functions/wp_upload_dir/

  $upload = wp_upload_dir();
  $upload_dir = $upload['basedir'];
  $upload_dir_dsi = $upload_dir . '/dynamic-share-images';
  if (! is_dir($upload_dir_dsi)) { mkdir( $upload_dir_dsi, 0700 ); }

  $upload_dir_temp  = $upload_dir_dsi . '/temp';
  if (! is_dir($upload_dir_temp)) { mkdir( $upload_dir_temp, 0700 ); }

  $upload_dir_cache = $upload_dir_dsi . '/cache';
  if (! is_dir($upload_dir_cache)) { mkdir( $upload_dir_cache, 0700 ); }

  return true ;
  
}



/**
 * 
 * 
 */

function dsimages_has_share_image( $post ){

  $filename = dsimages_get_media_folder_path() . '/cache/' . $post->ID . '-' .$post->post_name.'.png' ;
  $file = file_get_contents ( $filename ) ;
  if( false != $file ){ return true ; }

  return false ;

}


/**
 * 
 * 
 */

function dsimages_generate_share_image( $post ){

  // TODO : support Gmagick
  // if( ! class_exists('Gmagick') ){ return false ; } 

  if( ! class_exists('Imagick') ){ return false ; }

  
  /**
   * TODO
   * true content && php template && layout
   * 
   */

  require_once __DIR__ . '/vendor/autoload.php';

  // https://github.com/dompdf/dompdf

  $dompdf = new \Dompdf\Dompdf();
  $dompdf->loadHtml('<h1>hello world</h1>');
  $dompdf->render();

  $output = $dompdf->output();
  file_put_contents( dsimages_get_media_folder_path() . '/temp/temp.pdf', $output);

  $imagick = new Imagick();
  $imagick->readImage( dsimages_get_media_folder_path() . '/temp/temp.pdf' );
  $imagick->writeImages( dsimages_get_media_folder_path() . '/cache/' . $post->ID . '-' .$post->post_name.'.png', false );

  unlink( dsimages_get_media_folder_path() . '/temp/temp.pdf' );

}