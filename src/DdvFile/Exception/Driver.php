<?php

namespace DdvPhp\DdvFile\Exception;

class Driver extends \DdvPhp\DdvFile\Exception
{
  // 魔术方法
  public function __construct( $message = 'Driver data error', $errorId = 'DRIVER_ERROR' , $code = '400' )
  {
    parent::__construct( $message , $errorId , $code );
  }
}