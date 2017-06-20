<?php

namespace DdvPhp\DdvFile\Exception;

class Database extends \DdvPhp\DdvFile\Exception
{
  // 魔术方法
  public function __construct( $message = 'Database data error', $errorId = 'DATABASE_ERROR' , $code = '400' )
  {
    parent::__construct( $message , $errorId , $code );
  }
}