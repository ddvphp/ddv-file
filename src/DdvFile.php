<?php

namespace DdvPhp;
use const null;
use DdvPhp\DdvFile\Core as DdvCore;

/**
 * Class DdvFile
 *
 * Wrapper around PHPMaile
 *
 * @package DdvPhp\DdvFile
 */
class DdvFile extends \DdvPhp\DdvFile\Core\Base
{
  /**
   * [第一步]获取文件分块建议大小
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-10T16:29:07+0800
   * @param    array                    $data [description]
   * @return   [type]                         [description]
   */
  public function getPartSize(array $data=array()){
    return DdvCore\GetPartSize::run($this->call, $this->attr, $data);
  }
  /**
   * [第二步]获取文件上传fileId
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-10T16:28:25+0800
   * @param    array                    $data [description]
   * @return   [type]                         [description]
   */
  public function getFileId(array $data=array()){
    return DdvCore\GetFileId::run($this->call, $this->attr, $data);
  }
  /**
   * [第三步]获取文件块信息
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-10T20:16:18+0800
   * @param    array                    $data [description]
   * @return   [type]                         [description]
   */
  public function getFilePartInfo(array $data=array()){
    return DdvCore\GetFilePartInfo::run($this->call, $this->attr, $data);
  }
  public function getFileIdInputKeys(array $extendKeys=array()){
    return array_merge($this->sysGetFileIdInputKeys, $extendKeys);
  }

}
