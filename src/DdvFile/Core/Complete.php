<?php

namespace DdvPhp\DdvFile\Core;
use \DdvPhp\DdvFile\Exception\Input as InputException;


/**
 * Class Complete
 *
 * Wrapper around PHPMaile
 *
 * @package DdvPhp\DdvFile\Core
 */
final class Complete
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
    $fileInfo = $db->getFileInfoByFileID((string)$data['fileId']);
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
      'isUploadEnd' => (bool)($fileInfo['status'] === 'OK')
    );

    if ($resData['isUploadEnd']) {
      // 如果已经上传完成，直接结束
      return $resData;
    }
    // 获取存储驱动
    $driver = $attr('driver');

    $driver->completeMultipartUpload($fileInfo['path'], (string)$fileInfo['upload_id']);

    $tempData = array(
      // 状态修改
      'status'=>'OK'
    );
    // 更新数据库
    $db->updateFileInfoByFileID((string)$fileInfo['id'], $tempData);
    // 释放数据
    unset($tempData);

    $resData['isUploadEnd'] = true;

    return $resData;
  }
}
?>
