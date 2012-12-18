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
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <link rel="stylesheet" href="http://yandex.st/highlightjs/7.3/styles/arta.min.css">
    <script src="http://yandex.st/highlightjs/7.3/highlight.min.js"></script>
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
      pre code {
        margin: 2px 0px 0px 5px;
      }
      #wrapper {
        width: 800px;
      }
      
      .toc ul {
        margin: 0px;
        padding: 0px 0px 0px 22px;
      }
      .toc ul li {
        list-style-type: none;
        margin: 2px 0px;
        padding: 0px;
      }
      .toc ul li a {
        color: #333333;
      }
      
      .paramblock {
        padding: 2px 9px;
      }
      .paramlist {
        width: 100%;
      }
      .paramlist td {
        vertical-align: top;
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
    <script type="text/javascript">
$(document).ready(function() {
  $('pre code').each(function(i, e) {hljs.highlightBlock(e)});
});
    </script>
  </head>
  <body>
    <div class="btt" onclick="window.scrollTo(0,0);window.location.hash='';">Back To Top</div>
    <div id="wrapper" id="top">
      <h1>Dependancies</h1>
      
<?php
  $opts = array(
    'config' => 'socialapidocs/Configuration.json',
    'errors' => 'socialapidocs/Errors.json',
    'deps' => 'socialapidocs/deps.png'
  );
  $docgen = new JsonDocGen( $opts );
  $docgen->generate( );
?>
    </div>
  </body>
</html>

    