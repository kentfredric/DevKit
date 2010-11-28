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

  public function caller_string( $frameskip = 0 , $depth = -1 ){
    $frameskip += 1;
    $backtrace = debug_backtrace();
    if( $depth < 0 ){ 
      $depth = count(  $backtrace );
    }
    $backtrace = array_slice( $backtrace, $frameskip, $depth, true );
    $out = array();
    foreach( $backtrace as $index => $v ){ 
      array_push( $out, "#$index : {$v['file']} @ {$v['line']} : {$v['function']} (" .
         implode(",", $v['args']) . ")");
    }
    return implode("\n", $out );
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
      $this->diag($this->caller_string());
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
      $this->diag($this->caller_string());
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
      $this->diag($this->caller_string());
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
      $this->diag($this->caller_string());
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
      $this->diag($this->caller_string());
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
      $this->diag($this->caller_string());
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
      $this->diag($this->caller_string());
    }
  }

  public function done_testing(){
    print "1.." . $this->current;
  }
}
