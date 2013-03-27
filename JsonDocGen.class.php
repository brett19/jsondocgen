<?php
    class JsonDocGen
    {
      private $_jsonPath = '';
      private $_errorsPath = '';
      private $_depsPath = '';
      
      public function __construct( $opts ) 
      {
        $this->_jsonPath = $opts['config'];
        $this->_errorsPath = $opts['errors'];
        $this->_depsPath = $opts['deps'];
      }
            
      public function generatePV( $params )
      {
        echo "<table class=\"paramlist\">\n";
        echo "<tr><td colspan=\"2\"><hr /></td></tr>";
        foreach( $params as $pkey => $param ) {
          echo "<tr>\n";
          echo "<td>";
          if( $pkey[0] != '_' ) {
            echo "<b>$pkey</b><br />";
          }
          echo "<span class=\"subinfo\">";
          echo "[$param[type]]<br />";
          echo ((isset($param['optional'])&&$param['optional'])?'optional':'required') . "<br />";
          echo "</span>";
          echo "</td>\n";
          echo "<td>";
          if( isset($param['description']) ) {
            echo "$param[description]<br />";
          }
          if( isset($param['example']) ) {
            echo "<br />";
            echo "<b>Example Value:</b> $param[example]<br />";
          }
          if( isset($param['subobj']) ) {
            echo "<br />\n";
            $this->generatePV( $param['subobj'] );
          }
          echo "</td>";
          echo "</tr>\n";
          echo "<tr><td colspan=\"2\"><hr /></td></tr>";
        }
        echo "</table>\n";
      }
      
      public function generateVars( $params )
      {
        $this->generatePV( $params );
      }
      
      public function generateParams( $params )
      {
        $this->generatePV( $params );
      }
      
      public function generateEndpoint( $ep )
      {
        $href = $this->getTocName('ep',$ep['name']);
        echo "<div id=\"$href\">\n";
        echo "<h3>$ep[name]</h3>\n";
        echo "$ep[description]<br />\n";
        echo "<br />";
        echo "<span class=\"subinfo\"><b>Implemented:</b> " . ((!isset($ep['implemented'])||$ep['implemented'])?'Yes':'No') . "</span><br />\n";
        echo "<span class=\"subinfo\"><b>Authenticated:</b> " . ((isset($ep['authenticated'])&&$ep['authenticated'])?'Yes':'No') . "</span><br />\n";
        
        echo "<h4> <i>$ep[method]</i> $ep[uri] </h4>\n";

        if( isset($ep['parameters']) ) {
          echo "<u>Parameters:</u><br />\n<div class=\"paramblock\">\n";
          $this->generateParams( $ep['parameters'] );
          echo "</div><br />\n";
        }
        if( isset($ep['body']) ) {
          echo "<u>Request Body:</u><br />\n<div class=\"paramblock\">\n";
          $this->generateVars( $ep['body'] );
          echo "</div><br />\n";
        }
        if( isset($ep['resp']) ) {
          echo "<u>Response Body:</u><br />\n<div class=\"paramblock\">\n";
          if( isset($ep['respType']) && $ep['respType'] == 'list' ) {
            $this->generateVars(array(
              "_1" => array( 
                "type" => "array(object)",
                "subobj" => $ep['resp']
              )
            ));
          } else {
            $this->generateVars( $ep['resp'] );
          }
          echo "</div><br />\n";
        }
        
        if( isset($ep['example']) ) {
          $ex = $ep['example'];
          echo "<div id=\"$href-example\">\n";
          echo "<u>Example:</u><br />\n";
          echo "<pre>";
          echo "$ep[method] $ex[uri]\n";
          echo "<code class=\"json\">";   
          if( isset($ex['request']) ) {
            $json = json_encode($ex['request'], JSON_PRETTY_PRINT );
            $json = '' .  str_replace("\n", "\n", $json);
            echo $json . "\n";
          }
          echo "</code>";
          echo "\n";
          
          echo "200 OK\n";
          echo "<code class=\"json\">";
          if( isset($ex['resp']) ) {
            $json = json_encode($ex['resp'], JSON_PRETTY_PRINT );
            $json = '' .  str_replace("\n", "\n", $json);
            echo $json . "\n";
          }
          echo "</code>";
          
          echo "</code></pre>";
          echo "</div>\n";
        }
        
        echo "</div>\n";
        echo "<br />";
      }
      
      public function generateNamespace( $ns )
      {
        $href = $this->getTocName('ns',$ns['name']);
        echo "<div id=\"$href\">\n";
        echo "<h2>$ns[name]</h2>\n";
        
        if( isset($ns['endpoints']) ) {
          foreach( $ns['endpoints'] as $ep ) {
            $this->generateEndpoint( $ep );
          }
        }
        
        echo "</div>\n";
      }
      
      public function generateENamespace( $ns )
      {
        $href = $this->getTocName('ens',$ns['name']);
        echo "<div id=\"$href\">\n";
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
        
        echo "</div>\n";
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
    
        echo "<div id=\"t_toc\" class=\"toc\">";
        echo "<h1>Table of Contents</h2>";
        echo "<ul>\n";
        echo "<li><a href=\"#t_api\">REST Endpoints</a></li>\n";
        echo "<ul>\n";
        foreach( $spec as $ns ) {
          $this->generateNamespaceToc( $ns );
        }
        echo "</ul>\n";
        echo "<li><a href=\"#t_errors\">Error Codes</a></li>\n";
        echo "<ul>\n";
        foreach( $errorSpec as $ns ) {
          $this->generateENToc( $ns );
        }
        echo "</ul>";
        echo "<li><a href=\"#t_deps\">Dependency Graph</a></li>\n";
        echo "</ul>";
        echo "</div>\n";
        echo "<br />";
        
        echo "<div id=\"t_api\">\n";
        echo "<h1>REST Endpoints</h2>";
        foreach( $spec as $ns ) {
          $this->generateNamespace( $ns );
        }
        echo "</div>";
        echo "<br />";
        
        echo "<div id=\"t_errors\">\n";
        echo "<h1>Error Codes</h2>";
        foreach( $errorSpec as $ns ) {
          $this->generateENamespace( $ns );
        }
        echo "</div>";
        echo "<br />";
        
        echo "<div id=\"t_deps\">\n";
        echo "<h1>Dependency Graph</h2>";
        $depimg = base64_encode(file_get_contents($this->_depsPath));
        echo "<img src=\"data:image/png;base64,$depimg\" />";
        echo "</div>";
        echo "<br />";
      }
    }
?>