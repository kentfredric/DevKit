<?php
/** This is a somewhat advanced/featureful autoloader.
 *
 * require(dirname(__FILE__).'/lib/DevKit/Autoload.php');
 *
 * # Always omit the trailing /, that way My_Class will expand to My/Class.php
 * # and My_Class_Foo will expand to My/Class/Foo.php
 * # If you use a trailing / , expect this to happen instead:
 * # My_Class -> My/Class/.php
 * # My_Class_Foo -> My/Class//Foo.php
 * # That might be what you want, but I doubt it.
 *
 * DevKit_Autoload::setprefix('My_Class', dirname(__FILE__) . '/../../foo/bar/My/Class');
 * ... later ..
 *
 * new My_Class_Baz() # just works.
 *
 *
 */

if(! defined('DEVKIT_AUTOLOAD') && !class_exists('DEVKIT_AUTOLOAD', false )){
  define('DEVKIT_AUTOLOAD', 1 );

class DevKit_Autoload {

  public static $_prefix   = array();
  public static $_hardpath = array();
  public static $_debug    = false;

  private static function debug(){

    if( !( self::$_debug || getenv('DEVKIT_AUTOLOAD_DEBUG') ) ){
      return;
    }
    $args      = func_get_args();
    $content   = implode("", $args);
    $lines     = explode("\n", $content );
    $fulllines = array_filter( $lines );
    $data      = '[DevKit_Autoload] ' . implode("\n  > ", $fulllines) . "\n";
#    print $data;
    fwrite(STDERR, $data);
  }

  /**
   *  DevKit_Autoload::setup();
   *
   *  Injects the autoload magic into PHP.
   *
   */
  public static function setup( ){

    ini_set( 'unserialize_callback_func' , 'spl_autoload_call' );
    spl_autoload_register( array( new self, 'autoload' ) );
    self::setprefix('DevKit', dirname(__FILE__) );

  }

  private static function prioritize_path( $left, $right ){
    $ll = strlen( $left );
    $lr = strlen( $right );
    if(  $ll > $lr ){
      return -1;
    }
    if( $ll < $lr ){
      return 1;
    }
    return 0;
  }

  public static function _try_hardpath( $class ) {
      self::debug("$class] Hardpath for $class");
      $path = self::$_hardpath[$class];
      if( file_exists( $path ) ){
        self::debug("$class] Hardpath found");
        return $path;
      }
      self::debug("$class] Hardpath not found");
      return false;
  }

  public static function _get_prefix( $class, $assumedPrefix ){
    if( $assumedPrefix == '' ){
      self::debug("$class] Prefix match for $class : emptyPrefix ");
      return array( '', $class );
    }
    $prefix_point = strpos( $class , $assumedPrefix );
    if( $prefix_point !== 0 ){
        return false;
    }
    if( ( strpos( $class, $assumedPrefix . '_' ) !== 0 )
           &&
        ( $class !== $assumedPrefix  )
      ){
        # this ignores things that Match the head,
        # but are not exact matches or child( $v . '_' ) matches .
        return false;
    }
    self::debug("$class] Prefix match for $class : '$assumedPrefix' ");
    $prefix_length = strlen( $assumedPrefix );
    $suffix = substr( $class, $prefix_length );
    return array( $assumedPrefix, $suffix );
  }
  /**
   * The guts of the autoloader, but doesn't do require on its own.
   */
  public static function discover( $class ){

    if( array_key_exists( $class , self::$_hardpath ) ){
      if( $result = self::_try_hardpath($class) ){
        return $result;
      }
    }
    foreach( self::$_prefix as $i => $v ){
      $prefix_data = self::_get_prefix( $class, $i );
      if( !$prefix_data ){
        continue;
      }
      list($prefix,$suffix) = $prefix_data;
      $file   = false;

      if( strlen( $suffix ) === 0 ){
        $file = $v . '.php';
      }

      $file = $v . str_replace('_','/',$suffix) . '.php';
      self::debug("$class] Prefix expansion: $file");
      if( file_exists( $file ) ){
        self::debug("$class] Prefix found");
        return $file;
      }
      self::debug("$class] Prefix not found, continuing");
    }
    self::debug("$class] No prefix registered matching $class");
    return false;
  }
  /**
   * DevKit_Autoload::autoload("Foo_Bar");
   *
   * Tries to get Foo_Bar into memory.
   */
  public static function autoload( $class ){

    $file = self::discover( $class );
    if( $file === false ){
      return false;
    }
    require $file;
    return true;
  }

  /**
   *
   * DevKit_Autoload::setpath( 'Foo_Bar', dirname(__FILE__) . '/foobar.php' );
   * DevKit_Autoload::autoload( 'Foo_Bar') # loads that module.
   *
   * Benefit: Lazy-loads module only as-needed, but you don't have to sacrifice the
   * capacity to hard-code where it is stored.
   */
  public static function setpath( $class, $path ){
    self::$_hardpath[ $class ] = $path;
  }

  /**
   *
   *  DevKit_Autoload::setprefix( 'Foo_Bar' , dirname(__FILE__) . '/lib/Foo/Bar' );
   *  DevKit_Autoload::autoload( 'Foo_Bar' ) # dispatches as /lib/Foo/Bar.php
   *  DevKit_Autoload::autoload( 'Foo_Bar_Baz' ) # dispatches as /lib/Foo/Bar/Baz.php
   *
   * This is a way of permitting autoloading of multiple libraries without needing them
   * to all coexist in the one tree.
   *
   * The idea being, you have all your custom code in one tree, but all the consumed
   * in another, but still being able to smartly load from both.
   *
   * All prefixes that match are tested in order of *Most* specific to *least* specific.
   * We're not sure if this is wise yet or not for the case where 2 identical classes exist,
   * but if you need to selectively override the behaviour, there's always the hardpath.
   *
   */
  public static function setprefix ( $prefix, $path ){
    self::$_prefix[$prefix] = $path;
    uksort( self::$_prefix , array( 'self', 'prioritize_path' ));
  }
}

DevKit_Autoload::setup();

}
