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
        } catch (\DdvPhp\DdvException\Error $e) {
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
    self::checkDate($call, $attr, $data);
    $db = $attr('database');
    // 试图获取文件信息，通过 md5、crc32、sha1
    $fileInfo = $db->getListsByCrc32Sha1Md5(0, 1, $data['fileMd5'], $data['fileSha1'], $data['fileCrc32'], 'OK');
  }
  private static function checkDate(\Closure $call, \Closure $attr, array &$data){
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
  }
}
?>
