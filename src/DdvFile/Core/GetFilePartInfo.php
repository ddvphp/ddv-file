<?php

namespace DdvPhp\DdvFile\Core;
use const null;
use \DdvPhp\DdvFile\Exception\Input as InputException;


/**
 * Class GetFilePartInfo
 *
 * Wrapper around PHPMaile
 *
 * @package DdvPhp\DdvFile\Core
 */
final class GetFilePartInfo
{
  /**
   * @author 桦 <yuchonghua@163.com>
   * @DateTime 2016-04-12T15:23:14+0800
   * @param    [array]$data = array();
   * @return   [type]
   */
  public static function run(\Closure $call, \Closure $attr, array &$data){

    if(!is_array($data)){
      throw new InputException('请检查上传参数','INPUT_DATA_ERROR');
    }
    
    if(empty($data['fileId'])){
      throw new InputException('文件SHA1错误','FILE_SHA1_ERROR');
    }
    // 获取数据库模型
    $db = $attr('database');
    // 试图获取文件信息
    $fileInfo = $db->getFileInfoByFileID((string)$data['fileId']);
    if ($fileInfo['fileCrc32'] !== $data['fileCrc32']) {
      throw new InputException('文件密匙值错误','UPLOAD_CRC32_ERROR');
    }
    if ($fileInfo['fileMd5'] !== $data['fileMd5']) {
      throw new InputException('文件密匙值错误','UPLOAD_MD5_ERROR');
    }
    if ($fileInfo['fileSha1'] !== $data['fileSha1']) {
      throw new InputException('文件密匙值错误','UPLOAD_CHA1_ERROR');
    }
    // 构建返回数据
    
    $resData = array(
      'file_size' => $fileInfo['file_size'],
      'part_size' => $fileInfo['part_size'],
      'part_sum'  => $fileInfo['part_sum'],
      'doneParts'=>array(),
      'isUploadEnd' => (bool)($fileInfo['status'] === 'OK')
    );

    // 获取存储驱动
    $driver = $attr('driver');
    // 如果没有上传id
    if (empty($fileInfo['upload_id'])) {
      $tempData = array(
        // 获取上传id
        'upload_id'=>$driver->getUploadId($fileInfo['file_path'])
      );
      // 更新数据库
      $db->updateFileInfoByFileID((string)$data['fileId'], $tempData);
      // 释放数据
      unset($tempData);
    }

    return $resData;
  }
}
?>
