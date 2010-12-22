<?php 

class DevKit_ErrorHandler { 

  public static function _exception_map() { 
    $array = array( 
      1 => 'Error',
      2 => 'Warning',
      4 => 'Parse',
      8 => 'Notice',
      16 => 'Core_Error',
      32 => 'Core_Warning',
      64 => 'Compile_Error',
      128 => 'Compile_Warning',
      256 => 'User_Error',
      512 => 'User_Warning',
      1024 => 'User_Notice', 
      2048 => 'Strict',
      4096 => 'Recoverable_Error',
      8192 => 'Deprecated', 
      16384 => 'User_Deprecated',
    );
    return $array;
  }

  public static function _class_for_error( $code ){
    $array = self::_exception_map();
    return 'DevKit_Exception_PHP_' . $array[$code];
  }

  public function handle_error( $errno, $errstr, $errfile, $errline ){ 
    throw new ErrorException( $errstr, 0, $errno, $errfile, $errline );
  }

  public static function setup(){
    $instance = new self;
    set_error_handler(array($instance, 'handle_error'));
  }

}
