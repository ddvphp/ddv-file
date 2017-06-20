<?php

namespace DdvPhp\DdvFile\Exception;

class Sys extends \DdvPhp\DdvFile\Exception
{
  // 魔术方法
  public function __construct( $message = 'System error', $errorId = 'SYSTEM_ERROR' , $code = '500' )
  {
    parent::__construct( $message , $errorId , $code );
  }
}