<?php

namespace DdvPhp\DdvFile\Core;
use const null;
use \DdvPhp\DdvFile\Exception\Input as InputException;


/**
 * Class GetFileId
 *
 * Wrapper around PHPMaile
 *
 * @package DdvPhp\DdvFile\CoreExtends\Util
 */
final class GetFileId
{
  /**
   * @author 桦 <yuchonghua@163.com>
   * @DateTime 2016-04-12T15:23:14+0800
   * @param    [array]$data = array();
   * @return   [type]
   */
  public static function run(\Closure $call, \Closure $attr, array &$data){
    //多维数据检测
    if (isset($data[0])) {
      $r=array();
      foreach ($data as $key => $file) {
        $rdata = array();
        try {
          $rdata=self::getOne($call, $attr, $data[$key]);
        } catch (\DdvPhp\DdvFile\Exception $e) {
          $rdata['statusCode'] = method_exists($e,'getCode') ? $e->getCode() : 500;
          $rdata['error_id'] = method_exists($e,'getErrorId') ? $e->getErrorId() : 'UNKNOWN_ERROR';
          $rdata['msg'] = $e->getMessage();
          $rdata['msg'] = empty($rdata['msg'])?'':$rdata['msg'];
        }
        $r[] = $rdata;
      }
    }else{
      $r = self::getOne($call, $attr, $data);
    }
    return $r;
  }
  private static function getOne(\Closure $call, \Closure $attr, array &$data){
    // 检测数据
    $data['fileSha1'] = isset($data['fileSha1'])?strtoupper($data['fileSha1']):'';
    $data['fileMd5'] = isset($data['fileMd5'])?strtoupper($data['fileMd5']):'';
    $data['filePartMd5Lower'] = isset($data['filePartMd5Lower'])?strtoupper($data['filePartMd5Lower']):'';
    $data['filePartMd5Upper'] = isset($data['filePartMd5Upper'])?strtoupper($data['filePartMd5Upper']):'';
    $data['fileCrc32'] = isset($data['fileCrc32'])?strtoupper($data['fileCrc32']):'';
    $data['fileType'] = empty($data['fileType'])?'application/octet-stream':$data['fileType'];
    $data['fileName'] = empty($data['fileName'])?mt_rand(100000,9999999):$data['fileName'];
    $data['lastModified'] = intval($data['lastModified']);
    if(empty($data['fileCrc32'])/*|| !preg_match('/^([\dA-F]{40})$/', $data['fileCrc32'])*/){
      throw new InputException('文件CRC32错误','FILE_CRC32_ERROR');
    }
    if(empty($data['fileSha1'])|| !preg_match('/^([\dA-F]{40})$/', $data['fileSha1'])){
      throw new InputException('文件SHA1错误','FILE_SHA1_ERROR');
    }
    if(empty($data['fileMd5'])|| !preg_match('/^([\dA-F]{32})$/', $data['fileMd5'])){
      throw new InputException('文件MD5错误','FILE_MD5_ERROR');
    }
    if(empty($data['filePartMd5Lower'])|| !preg_match('/^([\dA-F]{32}-[\d]+)$/', $data['filePartMd5Lower'])){
      throw new InputException('文件MD5错误','FILE_PART_MD5_LOWER_ERROR');
    }
    if(empty($data['filePartMd5Upper'])|| !preg_match('/^([\dA-F]{32}-[\d]+)$/', $data['filePartMd5Upper'])){
      throw new InputException('文件MD5错误','FILE_PART_MD5_UPPER_ERROR');
    }
    if (empty($data['fileSize']) || !is_numeric($data['fileSize'])) {
      throw new InputException('文件大小错误','FILE_SIZE_ERROR');
    }
    if (empty($data['deviceType']) || !in_array(strtolower($data['deviceType']), $attr('sysDeviceType'))) {
      throw new InputException('暂时不支持该设备类型','DEVICE_TYPE_ERROR');
    }

    // 获取数据库模型
    $db = $attr('database');
    // 获取uid
    $uid = $attr('uid');
    // 默认文件id为空
    $fileId = null;
    // 返回数据
    $resData = array(
      'fileMd5' => $data['fileMd5'],
      'fileSha1' => $data['fileSha1'],
      'fileCrc32' => $data['fileCrc32'],
      'isUploadEnd' => false
    );
    // 如果有uid
    if ($uid) {
      try {
        // 试图获取该用户是否上传过该文件
        $fileId = $db->getFileIdByCrc32Sha1Md5Uid($data['fileMd5'], $data['fileSha1'], $data['fileCrc32'], (string)$attr('uid'));
      } catch (\DdvPhp\DdvFile\Exception\Database $e) {
        // 容错
      }
    }
    // 如果还是没有找到文件id
    if (!$fileId) {
      try {
        // 试图获取文件信息，通过 md5、crc32、sha1，查找上传好的文件
        $lists = $db->getListsByCrc32Sha1Md5(0, 10, $data['fileMd5'], $data['fileSha1'], $data['fileCrc32'], 'OK');
        foreach ($lists as $key => $fileInfo) {
          // 如果文件的这个签名也是相同，就直接使用该文件fileId
          if($fileInfo['filePartMd5Lower'] === $data['filePartMd5Lower'] && $fileInfo['filePartMd5Upper'] === $data['filePartMd5Upper']){
            $fileId = $fileInfo['id'];
            break;
          }
          unset($fileInfo);
        }
      } catch (\DdvPhp\DdvFile\Exception\Database $e) {
        // 容错
      }
    }
    $fileInfo = null;
    // 如果有文件id
    if ($fileId) {
      try {
        // 试图获取文件信息
        $fileInfo = $db->getFileInfoByFileID((string)$fileId);
        // 输出文件id
        $resData['fileId'] = $fileId;
        // 输出文件原始相对路径
        $resData['sourcePath'] = $fileInfo['file_path'];
        // 返回文件的上传状态是否完成
        $resData['isUploadEnd'] = $fileInfo['status'] === 'OK';
      } catch (\DdvPhp\DdvFile\Exception\Database $e) {
        // 容错, 没有找到这个文件
      }
    }
    // 获取数据库模型
    $driver = $attr('driver');
    // 如果没有找到文件信息，估计只能创建全新的了
    if (!$fileInfo) {
      $tempData = $call('getPartSize', $data);
      $dbData = array(
        // 分块大小
        'part_size' => $tempData['partSize'],
        // 分块总数
        'part_sum' => $tempData['partSum'],
        // 扩展名
        'ext_name' => self::getExtension($data['fileName']),
        // 文件名
        'file_name'=> $data['fileName'],
        // 文件大小
        'file_size'=> $data['fileSize'],
        // 文件md5
        'file_md5'=> $data['fileMd5'],
        // 文件sha1
        'file_sha1'=> $data['fileSha1'],
        // 文件crc32
        'file_crc32'=> $data['fileCrc32'],
        // 文件类型
        'file_type'=> $data['fileType'],
        // 文件块小写md5集合的md5
        'file_part_md5_lower'=> $data['filePartMd5Lower'],
        // 文件块大写md5集合的md5
        'file_part_md5_upper'=> $data['filePartMd5Upper'],
        // 文件最后修改时间
        'last_modified'=> $data['lastModified'],
        // 创建时间
        'create_time'=> time(),
      );
      $dbData['file_path'] = method_exists($driver, 'getFilePath') ? $driver->getFilePath($data) : self::getFilePath($data);
      // 添加到数据库
      $fileId = (string)$db->insertFileInfo($dbData);
      // 输出文件原始相对路径
      $resData['sourcePath'] = $dbData['file_path'];
    }
    // 输出文件原url
    $resData['sourceUrl'] = $driver->getUrlByPath($resData['sourcePath']);
    // 判断是否使用文件索引系统
    if ($attr('fileIndex')===true) {
      // 文件索引模块开始
    }else{
      // 直接使用源路径
      $resData['path'] = $resData['sourcePath'];
      $resData['url'] = $resData['sourceUrl'];
    }
    // 返回结果
    return $resData;
  }
  public static function getFilePath($data){
    $fileNameNew = (string)bin2hex(random_bytes(3));
    // 组装新的文件名[不带后缀名]
    $fileNameNew .= substr( $data['fileMd5'], 4, 4 ) . substr( md5($data['filePartMd5Lower']), 4, 4 ) .substr( md5($data['filePartMd5Upper']), 4, 4 ) .substr( $data['fileSha1'], 8, 4 )  .substr( $data['fileCrc32'], 0, 4 ) ;
    $extName = empty($data['fileName']) ? '' : self::getExtension($data['fileName']);
    // 如果有扩展名就补上扩展名
    if (!empty($extName)) {
      $fileNameNew .= '.'.$extName;
    }
    $filePath = empty($data['directory'])?'/':$data['directory'];
    if (substr($filePath,-1) !== '/') {
      $filePath .= '/';
    }
    // 文件相对地址
    $filePath .= $fileNameNew;
    return $filePath;
  }
  public static function getExtension($file){
    return pathinfo($file, PATHINFO_EXTENSION);
  }
}
?>
