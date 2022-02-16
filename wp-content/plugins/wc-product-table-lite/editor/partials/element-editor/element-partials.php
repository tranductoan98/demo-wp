<?php
// locate and include partials - nav + cell template + heading content
$partials = array_diff( scandir( __DIR__ . '/partials'), array('..', '.', '.DS_Store') );
foreach( $partials as $partial ){
  if( substr($partial, -4) == '.php' ){
    echo '<script type="text/template" data-wcpt-partial="'. substr( $partial, 0, -4 ) .'">';
    if( 
      'add' != substr ( $partial , 0 , 3 ) ||
      'add_selected_to_cart.php' == $partial 
    ){
      $x1 = explode( '__', substr( $partial, 0, -4 ) );
      $elm_name = ucwords( implode( ' ', explode( '_', $x1[0] ) ) );

      switch ($elm_name) {
      case 'Apply Reset':
          $elm_name = 'Apply / Reset';
          break;

      case 'Html':
        $elm_name = 'HTML';
        break;          

      case 'Download Csv':
        $elm_name = 'Download CSV';
        break;                  
      }

      echo '<h2>Edit element: \''. $elm_name .'\'</h2>';
    }
    include( 'partials/' . $partial );
    echo '</script>';
  }
}
?>
