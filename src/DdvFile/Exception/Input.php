<?php

namespace DdvPhp\DdvFile\Exception;

class Input extends \DdvPhp\DdvFile\Exception
{
  // 魔术方法
  public function __construct( $message = 'Input data error', $errorId = 'INPUT_ERROR' , $code = '400' )
  {
    parent::__construct( $message , $errorId , $code );
  }
}