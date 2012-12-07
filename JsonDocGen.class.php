<?php
    class JsonDocGen
    {
      private $_jsonPath = '';
      private $_errorsPath = '';
      
      public function __construct( $jsonPath, $errorsPath ) 
      {
        $this->_jsonPath = $jsonPath;
        $this->_errorsPath = $errorsPath;
      }
            
      public function generateVP( $params )
      {
        foreach( $params as $pkey => $param ) {
          echo "| *$pkey*\n\\[$param[type]\\]\n" . ($param['optional']?'optional':'required');
          echo "| $param[description]\n*Example Value:* " . (isset($param['example'])?$param['example']:'');
          echo "|\n";
        }
      }
            
      public function generateVars( $params )
      {
        $this->generateVP( $params );

      }
      
      public function generateParams( $params )
      {
        $this->generateVP( $params );
      }
      
      public function generateEndpoint( $ep )
      {
        echo "h4. $ep[name]\n";
        echo "$ep[description]\n";
        echo "\n";
        echo "Implemented: " . ((!isset($ep['implemented'])||$ep['implemented'])?'Yes':'No') . "\n";
        echo "Authenticated: " . ((isset($ep['authenticated'])&&$ep['authenticated'])?'Yes':'No') . "\n";
        
        echo "bq. $ep[method] $ep[uri]\n";

        if( isset($ep['parameters']) ) {
          echo "Parameters:\n";
          $this->generateParams( $ep['parameters'] );
          echo "\n";
        }
        if( isset($ep['body']) ) {
          echo "Request Body:\n";
          $this->generateVars( $ep['body'] );
          echo "\n";
        }
        if( isset($ep['resp']) ) {
          echo "Response Body:\n";
          $this->generateVars( $ep['resp'] );
          echo "\n";
        }
        
        if( isset($ep['example']) ) {
          $ex = $ep['example'];
          echo "Example:\n";
          echo "{noformat}\n";
          echo "$ep[method] $ex[uri]\n";          
          if( isset($ex['request']) ) {
            $json = json_encode($ex['request'], JSON_PRETTY_PRINT );
            $json = '> ' .  str_replace("\n", "\n> ", $json);
            echo $json . "\n>\n";
          } else {
            echo ">\n";
          }
          echo "\n";
          
          echo "200 OK\n";
          if( isset($ex['resp']) ) {
            $json = json_encode($ex['resp'], JSON_PRETTY_PRINT );
            $json = '< ' .  str_replace("\n", "\n< ", $json);
            echo $json . "\n<\n";
          } else {
            echo "<\n";
          }
          
          echo "{noformat}\n";
        }
        
        echo "\n\n";
      }
      
      public function generateNamespace( $ns )
      {
        echo "h2. $ns[name]\n";
        
        if( isset($ns['endpoints']) ) {
          foreach( $ns['endpoints'] as $ep ) {
            $this->generateEndpoint( $ep );
          }
        }
        echo "\n";
      }
      
      public function generateENamespace( $ns )
      {
        echo "h2. $ns[name]\n";
        
        echo "||Code||Name||Description||\n";
        if( isset($ns['codes']) ) {
          foreach( $ns['codes'] as $er ) {
            echo "| $er[code]";
            echo "| $er[name]";
            echo "| $er[description]";
            echo "|\n";
          }
        }
        echo "\n";
      }
      
      public function generate( ) 
      {
        $jsonData = file_get_contents($this->_jsonPath);
        $spec = json_decode($jsonData, true);
        
        $jsonError = json_last_error();
        if( $jsonError != 0 ) {
          die( "API JSON Parser Error: $jsonError\n" );
        }
        
        $errorData = file_get_contents($this->_errorsPath);
        $errorSpec = json_decode($errorData, true);
        
        $jsonError = json_last_error();
        if( $jsonError != 0 ) {
          die( "Error JSON Parser Error: $jsonError\n" );
        }
        
        echo "h1. Table of Contents\n";
        echo "{toc}\n\n";
        
        echo "h1. REST Endpoints\n";
        foreach( $spec as $ns ) {
          $this->generateNamespace( $ns );
        }
        echo "<br />";
        
        echo "h1. Error Codes\n ";
        foreach( $errorSpec as $ns ) {
          $this->generateENamespace( $ns );
        }
      }
    }
?>