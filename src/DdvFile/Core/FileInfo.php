<?php

namespace DdvPhp\DdvFile\Core;
use \DdvPhp\DdvFile\Exception\Input as InputException;


/**
 * Class FileInfo
 *
 * Wrapper around PHPMaile
 *
 * @package DdvPhp\DdvFile\Core
 */
final class FileInfo
{
  public static function getUrlByPath(\Closure $call, \Closure $attr, $path){

      $driver = $attr('driver');
      return $driver->getUrlByPath($path);
  }
  /**
   * @author 桦 <yuchonghua@163.com>
   * @DateTime 2016-04-12T15:23:14+0800
   * @param    [array]$data = array();
   * @return   [type]
   */
  public static function getFileInfo(\Closure $call, \Closure $attr, $fileId){
    if(empty($fileId)){
      throw new InputException('文件id错误','FILE_ID_ERROR');
    }
    // 获取数据库模型
    $db = $attr('database');
    // 试图获取文件信息
    $fileInfo = $db->getFileInfo((string)$fileId);
    // 构建返回数据
    
    $resData = array();
    $dbKeyToCalssKey = $db->dbKeyToCalssKey;
    foreach ($fileInfo as $key => $value) {
      $key = empty($dbKeyToCalssKey[$key]) ? $key : $dbKeyToCalssKey [$key] ;
      $resData[$key] = $value;
    }
    return $resData;
  }
  /**
   * @author 桦 <yuchonghua@163.com>
   * @DateTime 2016-04-12T15:23:14+0800
   * @param    [array]$data = array();
   * @return   [type]
   */
  public static function updateFileInfo(\Closure $call, \Closure $attr, $fileId, array $data){
    if(empty($fileId)){
      throw new InputException('文件id错误','FILE_ID_ERROR');
    }
    $tempData = array(
      'update_time'=>time()
    );
    // 获取数据库模型
    $db = $attr('database');
    $calssKeyToDbKey = $db->calssKeyToDbKey;
    foreach ($data as $key => $value) {
      $key = empty($calssKeyToDbKey[$key]) ? $key : $calssKeyToDbKey [$key] ;
      $tempData[$key] = $value;
    }
    // 更新数据库
    $db->updateFileInfo((string)$fileId, $tempData);
    // 释放数据
    unset($tempData);
  }
}
?>
