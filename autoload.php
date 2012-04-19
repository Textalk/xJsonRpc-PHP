<?php
/**
 * @file
 * Include this file to register an spl_autoload for these classes.
 */


function xJsonRpc_autoload($class) {
  static $classfiles;
  if (!isset($classfiles)) {
    $basedir = dirname(__FILE__);

    $classfiles = array(
      'Jsonrpc20WebClientNotify'   => $basedir . '/client.php',
      'Jsonrpc20BatchRequest'      => $basedir . '/client.php',
      'Jsonrpc20WebClient'         => $basedir . '/client.php',
      'Jsonrpc20Server'            => $basedir . '/server.php',
      'StdinJsonrpc20Server'       => $basedir . '/server.php',
      'WebJsonrpc20Server'         => $basedir . '/server.php',
      'JsonrpcException'           => $basedir . '/exceptions.php',
      'JsonrpcParseError'          => $basedir . '/exceptions.php',
      'JsonrpcParseResponseError'  => $basedir . '/exceptions.php',
      'JsonrpcInvalidRequestError' => $basedir . '/exceptions.php',
      'JsonrpcInvalidVersionError' => $basedir . '/exceptions.php',
      'JsonrpcMethodNotFoundError' => $basedir . '/exceptions.php',
      'JsonrpcInvalidParamsError'  => $basedir . '/exceptions.php',
      'JsonrpcInternalError'       => $basedir . '/exceptions.php',
      'JsonrpcApplicationError'    => $basedir . '/exceptions.php',
    );
  }

  if (isset($classfiles[$class])) {
    require_once($classfiles[$class]);
    return;
  }
}

spl_autoload_register('xJsonRpc_autoload');
