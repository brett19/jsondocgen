<?php
    require_once( 'JsonDocGen.class.php' );
?>
    
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Platform API Documentation</title>
    <meta name="author" content="root" />
    <style type="text/css">
      body {
        font-family: Tahoma;
        font-size: 12px;
      }
      pre {
        background-color: #DDDDDD;
        width: 100%;
        padding: 4px;
      }
      #wrapper {
        width: 800px;
      }
      
      .paramlist {
        width: 100%;
        padding: 2px 9px;
      }
      .paramlist td:first-child {
        width: 100px;
      }
      .paramlist hr {
        border: 0px solid white;
        border-top: 1px solid black;
      }
      
      .errorlist {
        width: 100%;
        padding: 2px 9px;
      }
      .errorlist td:first-child {
        width: 100px;
      }
      .errorlist td:last-child {
        width: 460px;
      }
      .errorlist td {
        border: 1px solid #999999;
        padding: 5px;
      }

      .subinfo {
        color: #777777;
      }
      
      .btt {
        position: fixed;
        cursor: pointer;
        bottom: 20px;
        left: 830px;
        background-color: #bbbbbb;
        padding: 6px;
        color: #000000;
        text-decoration: underline;
      }

    </style>
  </head>
  <body>
    <div class="btt" onclick="window.scrollTo(0,0);window.location.hash='';">Back To Top</div>
    <a id="top">
      <div id="wrapper">
<?php
  $docgen = new JsonDocGen( '/var/www/html/SocialApiClient/Configuration.json', '/var/www/html/SocialApiClient/Errors.json' );
  $docgen->generate( );
?>
      </div>
    </a>
  </body>
</html>

    