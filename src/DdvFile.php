<?php

namespace DdvPhp;
use const null;

/**
 * Class DdvFile
 *
 * Wrapper around PHPMaile
 *
 * @package DdvPhp\DdvFile
 */
class DdvFile extends \DdvPhp\DdvFile\DdvFileBase
{
  public function __construct($config = null)
  {
    method_exists(parent::class, '__construct') && parent::__construct($config);
  }
  /**
   * [getFileIdOne 获取一个文件的上传id]
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2016-04-15T14:10:25+0800
   * @param    array                    $data [description]
   * @return   [type]                         [description]
   */
  public function getFileIdOne(array $data = array()){
    $this->getFileIdOneCheckInputData($data);
    

  }

}