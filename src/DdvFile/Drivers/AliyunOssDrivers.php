<?php
namespace DdvPhp\DdvFile\Drivers;
use \DdvPhp\DdvFile\Exception\Driver as DriverException;

/**
 * 
 */
class AliyunOssDrivers implements \DdvPhp\DdvFile\Drivers\HandlerInterface
{
  private $client;
  private $config;
  public function __construct($driverConfig){
    $this->config = $driverConfig;
  }
  public function open(){
    $this->client = new \OSS\OssClient($this->config['accessKeyId'], $this->config['accessKeySecret'], $this->config['endpoint'], $this->config['isCName'], $this->config['securityToken']);
    // $this->client = new \OSS\OssClient($accessKeyId, $accessKeySecret, $endpoint, $isCName = false, $securityToken = NULL);

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
  /**
   * 获取上传id
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-10T20:29:23+0800
   * @param    string                  $path [description]
   * @return   [type]                        [description]
   */
  public function getUploadId($path){
    $objectKey = $path;
    try{
      return $this->client->initiateMultipartUpload($this->config['bucket'], $objectKey);
    } catch(\OSS\Core\OssException $e) {
      throw new DriverException($e->getMessage(), 'GET_UPLOAD_ID');
    }
  }
  /**
   * 获取文件路径
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-10T20:38:01+0800
   * @param    [type]                   $data [description]
   * @return   [type]                         [description]
   */
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
  /**
   * 获取扩展名
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-10T20:37:52+0800
   * @param    [type]                   $file [description]
   * @return   [type]                         [description]
   */
  public function getExtension($file){
    return pathinfo($file, PATHINFO_EXTENSION);
  }
}