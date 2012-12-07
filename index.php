<?php
  require_once( 'JsonDocGen.class.php' );
  echo '<pre>';
  $docgen = new JsonDocGen( '/var/www/html/SocialApiClient/Configuration.json', '/var/www/html/SocialApiClient/Errors.json' );
  $docgen->generate( );
  echo '</pre>';
?>