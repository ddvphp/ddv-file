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
  /**
   * @author 桦 <yuchonghua@163.com>
   * @DateTime 2016-04-12T15:23:14+0800
   * @param    [array]$data = array();
   * @return   [type]
   */
  public static function getFileInfo(\Closure $call, \Closure $attr, $fileId){
    if(empty($data['fileId'])){
      throw new InputException('文件id错误','FILE_ID_ERROR');
    }
    // 获取数据库模型
    $db = $attr('database');
    // 试图获取文件信息
    $fileInfo = $db->getFileInfoByFileID((string)$fileId);
    // 构建返回数据
    
    $resData = array(
      // 分块大小
      'partSize'=>$fileInfo['part_size'],
      // 分块总数
      'partSum'=>$fileInfo['part_sum'],
      // 扩展名
      'fileExtName'=>$fileInfo['ext_name'],
      // 文件名
      'fileName'=>$fileInfo['name'],
      // 文件大小
      'fileSize'=>$fileInfo['size'],
      // 文件md5
      'fileMd5'=>$fileInfo['md5'],
      // 文件sha1
      'fileSha1'=>$fileInfo['sha1'],
      // 文件crc32
      'fileCrc32'=>$fileInfo['crc32'],
      // 文件类型
      'fileType'=>$fileInfo['type'],
      // 文件块小写md5集合的md5
      'filePartMd5Lower'=>$fileInfo['part_md5_lower'],
      // 文件块大写md5集合的md5
      'filePartMd5Upper'=>$fileInfo['part_md5_upper'],
      // 文件最后修改时间
      'lastModified'=>$fileInfo['last_modified'],
      // 创建时间
      'createTime'=>$fileInfo['create_time'],
    );
    return $resData;
  }
  /**
   * @author 桦 <yuchonghua@163.com>
   * @DateTime 2016-04-12T15:23:14+0800
   * @param    [array]$data = array();
   * @return   [type]
   */
  public static function updateFileInfo(\Closure $call, \Closure $attr, $fileId, array $data){
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
    $db->updateFileInfoByFileID((string)$fileId, $tempData);
    // 释放数据
    unset($tempData);
  }
}
?>
