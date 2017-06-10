<?php
namespace DdvPhp\DdvFile\Drivers;

/**
 * 
 */
class AliyunOssDrivers implements \DdvPhp\DdvFile\Drivers\HandlerInterface
{
  public function open($driverConfig){

  }
  public function close(){

  }
  /**
   * 文件path转url
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-10T19:37:20+0800
   * @param    [type]                   $path [description]
   * @return   [type]                        [description]
   */
  public function getUrlByPath($path){
    return 'http://xx.com/'.$path;
  }
  public function getFilePath($data){
    $fileNameNew = (string)bin2hex(random_bytes(3));
    // 组装新的文件名[不带后缀名]
    $fileNameNew .= substr( $data['fileMd5'], 4, 4 ) . substr( md5($data['filePartMd5Lower']), 4, 4 ) .substr( md5($data['filePartMd5Upper']), 4, 4 ) .substr( $data['fileSha1'], 8, 4 )  .substr( $data['fileCrc32'], 0, 4 ) ;
    $extName = empty($data['fileName']) ? '' : $this->getExtension($data['fileName']);
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
  public function getExtension($file){
    return pathinfo($file, PATHINFO_EXTENSION);
  }
}