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
            
      public function generateVars( $params )
      {
        echo "<table class=\"paramlist\">\n";
        echo "<tr><td colspan=\"2\"><hr /></td></tr>";
        foreach( $params as $pkey => $param ) {
          echo "<tr>\n";
          echo "<td>";
          echo "<b>$pkey</b><br />";
          echo "<span class=\"subinfo\">";
          echo "[$param[type]]<br />";
          echo ($param['optional']?'optional':'required') . "<br />";
          echo "</span>";
          echo "</td>\n";
          echo "<td>";
          echo "$param[description]<br />";
          echo "<br />";
          echo "<b>Example Value: </b>" . (isset($param['example'])?$param['example']:'') . "<br />";
          echo "</td>";
          echo "</tr>\n";
          echo "<tr><td colspan=\"2\"><hr /></td></tr>";
        }
        echo "</table>\n";
      }
      
      public function generateParams( $params )
      {
        echo "<table class=\"paramlist\">\n";
        echo "<tr><td colspan=\"2\"><hr /></td></tr>";
        foreach( $params as $pkey => $param ) {
          echo "<tr>\n";
          echo "<td>";
          echo "<b>$pkey</b><br />";
          echo "<span class=\"subinfo\">";
          echo "[$param[type]]<br />";
          echo "required<br />";
          echo "</span>";
          echo "</td>\n";
          echo "<td>";
          echo "$param[description]<br />";
          echo "<br />";
          echo "<b>Example Value: </b>" . (isset($param['example'])?$param['example']:'') . "<br />";
          echo "</td>";
          echo "</tr>\n";
          echo "<tr><td colspan=\"2\"><hr /></td></tr>";
        }
        echo "</table>\n";
      }
      
      public function generateEndpoint( $ep )
      {
        $href = $this->getTocName('ep',$ep['name']);
        echo "<a id=\"$href\">\n";
        echo "<h3>$ep[name]</h3>\n";
        echo "$ep[description]<br />\n";
        echo "<br />";
        echo "<span class=\"subinfo\"><b>Implemented:</b> " . ((!isset($ep['implemented'])||$ep['implemented'])?'Yes':'No') . "</span><br />\n";
        echo "<span class=\"subinfo\"><b>Authenticated:</b> " . ((isset($ep['authenticated'])&&$ep['authenticated'])?'Yes':'No') . "</span><br />\n";
        
        echo "<h4> <i>$ep[method]</i> $ep[uri] </h4>\n";

        if( isset($ep['parameters']) ) {
          echo "<u>Parameters:</u><br />\n";
          $this->generateParams( $ep['parameters'] );
          echo "<br />\n";
        }
        if( isset($ep['body']) ) {
          echo "<u>Request Body:</u><br />\n";
          $this->generateVars( $ep['body'] );
          echo "<br />\n";
        }
        if( isset($ep['resp']) ) {
          echo "<u>Response Body:</u><br />\n";
          $this->generateVars( $ep['resp'] );
          echo "<br />\n";
        }
        
        if( isset($ep['example']) ) {
          $ex = $ep['example'];
          echo "<u>Example:</u><br />\n";
          echo "<pre>";
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
          
          echo "</pre>";
        }
        
        echo "</a>\n";
        echo "<br />";
      }
      
      public function generateNamespace( $ns )
      {
        $href = $this->getTocName('ns',$ns['name']);
        echo "<a id=\"$href\">\n";
        echo "<h2>$ns[name]</h2>\n";
        
        if( isset($ns['endpoints']) ) {
          foreach( $ns['endpoints'] as $ep ) {
            $this->generateEndpoint( $ep );
          }
        }
        
        echo "</a>\n";
      }
      
      public function generateENamespace( $ns )
      {
        $href = $this->getTocName('ens',$ns['name']);
        echo "<a id=\"$href\">\n";
        echo "<h2>$ns[name]</h2>\n";
        
        echo "<table class=\"errorlist\">\n";
        echo "<tr>";
        echo "<th>Code</th>";
        echo "<th>Name</th>";
        echo "<th>Description</th>";
        echo "</tr>";
        if( isset($ns['codes']) ) {
          foreach( $ns['codes'] as $er ) {
            echo "<tr>";
            echo "<td>$er[code]</td>";
            echo "<td>$er[name]</td>";
            echo "<td>$er[description]</td>";
            echo "</tr>";
          }
        }
        echo "</table>\n";
        
        echo "</a>\n";
      }
      
      public function getTocName( $type, $name ) {
        return str_replace(' ','_',$type.'_'.$name);
      }
      
      public function generateEndpointToc( $ep )
      {
        $href = $this->getTocName('ep',$ep['name']);
        echo "<li><a href=\"#$href\">$ep[name]</a></li>\n";
      }
      
      public function generateNamespaceToc( $ns )
      {
        $href = $this->getTocName('ns',$ns['name']);
        echo "<li><a href=\"#$href\">$ns[name]</a></li>\n";
        
        if( isset($ns['endpoints']) ) {
          echo "<ul>\n";
          foreach( $ns['endpoints'] as $ep ) {
            $this->generateEndpointToc( $ep );
          }
          echo "</ul>\n";
        }
      }
      
      public function generateENToc( $ns )
      {
        $href = $this->getTocName('ens',$ns['name']);
        echo "<li><a href=\"#$href\">$ns[name]</a></li>\n";
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
    
        echo "<h1>Table of Contents</h2>";
        echo "<ul>\n";
        echo "<li>Platform API</li>\n";
        echo "<ul>\n";
        foreach( $spec as $ns ) {
          $this->generateNamespaceToc( $ns );
        }
        echo "</ul>\n";
        echo "</ul>";
        echo "<ul>\n";
        echo "<li>Error Codes</li>\n";
        echo "<ul>\n";
        foreach( $errorSpec as $ns ) {
          $this->generateENToc( $ns );
        }
        echo "</ul>";
        echo "</ul>";
        echo "<br />";
        
        echo "<h1>REST Endpoints</h2>";
        foreach( $spec as $ns ) {
          $this->generateNamespace( $ns );
        }
        echo "<br />";
        
        echo "<h1>Error Codes</h2>";
        foreach( $errorSpec as $ns ) {
          $this->generateENamespace( $ns );
        }
      }
    }
?>