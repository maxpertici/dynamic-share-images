<?php
/**
 * 
 * 
 * 
 */

// todo : remove uploads folder

$wp_upload_dir = wp_get_upload_dir() ;
$dsi_uploads_dir = $wp_upload_dir['baseurl'] . '/dynamic-share-images/' ;

dsimages_rrmdir( $dsi_uploads_dir ) ;

function dsimages_rrmdir( $dir ){ 
    if (is_dir($dir)) { 
      $objects = scandir($dir); 
      foreach ($objects as $object) { 
        if ($object != "." && $object != "..") { 
          if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object); 
        } 
      } 
      reset($objects); 
      rmdir($dir); 
    } 
 } 
