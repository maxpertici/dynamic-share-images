<?php


/**
 * Helpers pour le dossier d upload
 * return URL || false if folder dont exist
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
 * Helpers pour le dossier d upload
 * return PATH base || false if folder dont exist
 */

  function dsimages_get_media_folder_path(){

    if( dsimages_has_uploads_folder() ){
      $wp_upload_dir = wp_get_upload_dir() ;
      return  $wp_upload_dir['basedir'] . '/dynamic-share-images/' ;
    }

    return false ; 

  }
  
  
  

/**
 * test if upload folder exist
 * If NOT --> create folder
 * 
 * @source : https://developer.wordpress.org/reference/functions/wp_upload_dir/
 * 
 */
function dsimages_has_uploads_folder(){

  $upload           = wp_upload_dir();
  $upload_dir       = $upload['basedir'];
  $upload_dir_dsi   = $upload_dir . '/dynamic-share-images';
  $upload_dir_temp  = $upload_dir_dsi . '/temp';
  $upload_dir_cache = $upload_dir_dsi . '/cache';

  $create_folders = false ;
  if( ! is_dir( $upload_dir_dsi   )){ $create_folders = true ; }
  if( ! is_dir( $upload_dir_temp  )){ $create_folders = true ; }
  if( ! is_dir( $upload_dir_cache )){ $create_folders = true ; }

  if( true === $create_folders ){
    dsimages_create_uploads_folder();
  }

  return true ;

}

/**
 * 
 * 
 */
function dsimages_create_uploads_folder(){

  $upload           = wp_upload_dir();
  $upload_dir       = $upload['basedir'];
  $upload_dir_dsi   = $upload_dir     . '/dynamic-share-images';
  $upload_dir_temp  = $upload_dir_dsi . '/temp';
  $upload_dir_cache = $upload_dir_dsi . '/cache';

  if( ! is_dir( $upload_dir_dsi   )){ mkdir( $upload_dir_dsi,   0700 ); }
  if( ! is_dir( $upload_dir_temp  )){ mkdir( $upload_dir_temp,  0700 ); dsimages_create_htaccess( $upload_dir_temp ) ; }
  if( ! is_dir( $upload_dir_cache )){ mkdir( $upload_dir_cache, 0700 ); }

}


/**
 * 
 * 
 * 
 */

function dsimages_create_htaccess( $path ){

    // add hatccess
    $file    = $path . '/.htaccess';
    $content = '
      Order Deny,Allow
      Deny from all
    ';
  
    file_put_contents( $file, $content );

}





/**
 * Test if the share-image exist for this $post
 * 
 */

function dsimages_has_share_image( $post ){

  $filename = dsimages_get_media_folder_path() . '/cache/' . $post->ID . '-' .$post->post_name.'.png' ;
  $file = @fopen ( $filename, "r" ) ;
  if( false != $file ){ return true ; }

  return false ;
}


