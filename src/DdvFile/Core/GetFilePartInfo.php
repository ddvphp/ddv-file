<?php

namespace DdvPhp\DdvFile\Core;
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
      throw new InputException('文件id错误','FILE_ID_ERROR');
    }
    // 获取数据库模型
    $db = $attr('database');
    // 试图获取文件信息
    $fileInfo = $db->getFileInfo((string)$data['fileId']);
    if ($fileInfo['crc32'] !== $data['fileCrc32']) {
      throw new InputException('文件密匙值错误','UPLOAD_CRC32_ERROR');
    }
    if ($fileInfo['md5'] !== $data['fileMd5']) {
      throw new InputException('文件密匙值错误','UPLOAD_MD5_ERROR');
    }
    if ($fileInfo['sha1'] !== $data['fileSha1']) {
      throw new InputException('文件密匙值错误','UPLOAD_CHA1_ERROR');
    }
    // 构建返回数据
    
    $resData = array(
      'fileSize' => $fileInfo['size'],
      'partSize' => $fileInfo['part_size'],
      'partSum'  => $fileInfo['part_sum'],
      'doneParts'=>array(),
      'isUploadEnd' => (bool)($fileInfo['status'] === 'OK')
    );

    if ($resData['isUploadEnd']) {
      // 如果已经上传完成，直接结束
      return $resData;
    }
    // 获取存储驱动
    $driver = $attr('driver');

    try {
      if (!empty($fileInfo['upload_id'])) {
        $resData['doneParts'] = $driver->getUploadDoneParts($fileInfo['path'], (string)$fileInfo['upload_id']);
      }
    } catch (\DdvPhp\DdvFile\Exception\Driver $e) {
      $fileInfo['upload_id'] = '';
    }
    // 如果没有上传id
    if (empty($fileInfo['upload_id'])) {
      $tempData = array(
        // 获取上传id
        'upload_id'=>$driver->getUploadId($fileInfo['path'], array(
          'headers' => array(
            'Content-Type' => $fileInfo['type'],
            'Content-Disposition' => 'attachment; filename="'.$fileInfo['name'].'"'
          )
        ))
      );
      // 更新数据库
      $db->updateFileInfo((string)$fileInfo['id'], $tempData);
      // 释放数据
      unset($tempData);
      $resData['doneParts'] = array();
    }

    return $resData;
  }
}
?>
