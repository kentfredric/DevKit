<?php

/* Intented to be a Perl/TAP compatible-ish test library.
 *
 *
 */
class DevKit_Tester {

  public $output_handle = STDOUT;
  public $planned = null;
  public $current = 0;

  public function __construct( array $args = array() ){
    if( isset( $args['plan'] ) ) {
      $this->planned = $args['plan'];
    }
    if( $this->planned ){
      $this->_write_message( "1.." . $this->planned . "\n");
    }
  }

  public function _write_message( $message ){
    fwrite( $this->output_handle, $message );
  }
  public function plan(  $amount ){
    $this->planned = $amount;
    if( $this->planned ){
      $this->_write_message( "1.." . $this->planned . "\n");
    }
  }
  public function fail( $explanation = null ){
    $this->current += 1;
    if( $explanation ) {
      $this->_write_message( "not ok " . $this->current . " - " . $explanation . "\n" );
      $this->_write_message( "#\tFailed test '" . $explanation ."'\n");
    } else {
      $this->_write_message( "not ok " . $this->current . "\n");
      $this->_write_message( "#\tFailed test\n");
    }
  }
  public function pass( $explanation = null ){
    $this->current += 1;
    if( $explanation ){
      $this->_write_message("ok " . $this->current . " - " . $explanation . "\n");
    } else {
      $this->_write_message("ok " . $this->current . "\n");
    }
  }
  public function diag( $message = '' ){
    $lines = explode("\n", $message );
    foreach( $lines as $i => $v ){
      print "#\t$v\n";
    }
  }
  public function ok( $result,  $explanation = ''){
    if( $result ) {
      $this->pass( $explanation );
    } else {
      $this->fail( $explanation );
      $this->diag("ok(x) : true?( x )");
      $this->diag("Got : $result");
      $this->diag("Expected: a false value");
    }
  }

  public function is( $result, $expected, $explanation = '' ){
    if( $result == $expected ){
      $this->pass( $explanation );
    } else {
      $this->fail( $explanation );
      $this->diag("is( x, y ) : x == y ");
      $this->diag("Got '$result'");
      $this->diag("Expected '$expected'");
    }
  }
  public function isnt( $result, $expected, $explanation = '' ){
    if( $result != $expected ){
      $this->pass( $explanation );
    } else {
      $this->fail( $explanation );
      $this->diag("isnt( x, y ) : x != y ");
      $this->diag("Got '$result'");
      $this->diag("Expected anything but '$expected'");
    }
  }


  public function is_exactly( $result, $expected, $explanation = '' ){
    if( $result === $expected ){
      $this->pass( $explanation );
    } else {
      $this->fail( $explanation );
      $this->diag("is_exactly( x, y ) : x === y ");
      $this->diag("Got '$result'");
      $this->diag("Expected '$expected'");
      print_r( DevKit_Dumper::explain($result));
      print_r( DevKit_Dumper::explain($expected));
    }
  }
  public function isnt_exactly( $result, $expected, $explanation = '' ){
    if( $result !== $expected ){
      $this->pass( $explanation );
    } else {
      $this->fail( $explanation );
      $this->diag("isnt_exactly( x, y ) : x !== y ");
      $this->diag("Got '$result'");
      $this->diag("Expected anything but '$expected'");
      ( DevKit_Dumper::explain($result));
      print_r( DevKit_Dumper::explain($expected));
    }
  }

  public function like( $result, $expression, $message ){
    if( preg_match( $expression, $result ) ){
      $this->pass( $message );
    } else {
      $this->fail( $message );
      $this->diag("like( x , y ) :   x =~ y ");
      $this->diag("Got: '$result'");
      $this->diag("Expected matching: '$expression'");
    }
  }

  public function unlike( $result, $expression, $message ){
    if( !preg_match( $expression, $result ) ){
      $this->pass( $message );
    } else {
      $this->fail( $message );
      $this->diag("unlike( x , y ) :   x !~ y ");
      $this->diag("Got: '$result'");
      $this->diag("Expected anything but: '$expression'");
    }
  }

  public function done_testing(){
    print "1.." . $this->current;
  }
}
