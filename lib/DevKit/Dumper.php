<?php

class DevKit_Dumper { 

  private $iid = 0;
  private $dumperid = 0;
  private static $dumperidseq = 0;
  private $stash;
  private $gcstash;

  public function _explain_null(){ 
    return array( 'null', array() );
  }
  public function _explain_bool( &$item ){ 
    if ( $item ){ 
      return array( 'boolean', array( 'value' => 'true' ) );
    }
    return array('boolean', array('value' => 'false') );
  }
  public function _explain_integer( &$item ){
    return array('integer', array( 'value' => "$item" ) );
  }
  public function _explain_float( &$item ){
    return array('float', array('value' => "$item"));
  }
  public function _explain_string( &$item ){
    $length = strlen( $item );
    return array('string', array( 'length' => $length , 'value' => "$item" ));
  }

  public function _explain_array( &$item ){ 
    if( array_key_exists( '__DevKit_Dumper', $item ) && array_key_exists( $this->dumperid , $item['__DevKit_Dumper'] ) ){ 
      return array('array', array( 'id' => $item['__DevKit_Dumper'][ $this->dumperid ]['id'] ) );
    }
    if( !array_key_exists( '__DevKit_Dumper', $item )) {
      $item['__DevKit_Dumper'] = array();
    }
    $item['__DevKit_Dumper'][ $this->dumperid ] = array();

    $myid = ( $this->iid += 1 );
    $item['__DevKit_Dumper'][ $this->dumperid ]['id' ] = $myid;
    $this->gcstash[]= &$item;
    $output = array();
    $l = 0;
    foreach( $item as $i => &$v ){ 
      if( $i == '__DevKit_Dumper' ){ 
        continue;
      }
      $l++;
      $output[] = array('array_record', array( 'key' => $this->_explain( $i ), 'value' => $this->_explain($v ) ));
    }
    return array('array', array('items' => $l, 'id' => $myid , 'data' => $output ));
  }
  public function _explain_object( &$item ){ 
    $dumper = array();
    $id = spl_object_hash( $item ); 
    if( array_key_exists( $this->stash, $id ) ) {
      return array('object', array( 'id' => $this->stash[$id] ));
    }
    $myid = ( $this->iid += 1 );
    $this->stash[$id] = $myid;
    $properties = get_object_vars( $item );
    $output = array();
    $l = 0;
    foreach( $properties as $i => &$v ){ 
      if( $i == '__DevKit_Dumper' ){ 
        continue;
      }
      $l++;
      $output[] = array('property', array( 'name' => $this->_explain( $i ) , 'value' => $this->_explain($v ) ));
    }
    return array( 'object', array( 'properties' => $l, 'id' =>  $myid , 'data' => $output ));
  }
  private function &_gcstash(){ 
    if( isset( $this->gcstash ) ){ 
      return $this->gcstash;
    }
    $this->gcstash = array();
    return $this->gcstash;
  }
  public function _cleangc(){
    $stash = $this->_gcstash();
    foreach( $stash as $i => &$v ){ 
      unset( $v['__DevKit_Dumper'] );
    }
  }

  public function _explain( &$item ){
    switch(true){
      case is_null( $item )    : return $this->_explain_null();
      case is_scalar( $item )  :
        switch ( true ) { 
          case is_bool( $item )    : return $this->_explain_bool( $item );
          case is_integer( $item ) : return $this->_explain_integer( $item );
          case is_float( $item )   : return $this->_explain_float( $item );
          case is_string( $item )  : return $this->_explain_string( $item );
      }
      case is_array( $item ) :  return $this->_explain_array( $item );
      case is_object( $item ):  return $this->_explain_object( $item );
    }
    return array('unrecognized', array() );
  }

  public function _format( $data ){
    $indent = "  ";
    $lines = array();
    foreach( $data[1] as $label => $value ){
      if( !is_array( $value ) ){
        array_push( $lines, "$label: $value");
        continue;
      }
      if( $label === 'data' ){ 
        array_push( $lines, "$label:" );
        foreach( $value as $record ){
          $subresults = array_filter(explode("\n", $this->_format($record)));
          foreach( $subresults as $line ){
            array_push( $lines, "$indent$line");
          }
        }
        continue;
      }
      $subresults = array_filter(explode("\n",$this->_format($value)));
      array_push($lines, "$label:");
      foreach( $subresults as $line ){
          array_push( $lines, "$indent$line");
      }
    }
    foreach( $lines as $lineno => &$line ){ 
      $line = "$indent$line";
    }
    array_unshift( $lines , $data[0] . ":" );
    return implode("\n", $lines ) . "\n";
  }

  public static function explain( &$item ){ 
    $i = new self;
    self::$dumperidseq += 1;
    $i->dumperid = self::$dumperidseq;
    $output = $i->_format($i->_explain( $item ));
    $i->_cleangc();
    return $output;
  }

}
