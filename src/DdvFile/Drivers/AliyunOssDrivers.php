<?php
namespace DdvPhp\DdvFile\Drivers;
use \DdvPhp\DdvFile\Exception\Driver as DriverException;
use \OSS\OssClient as OssClient;

/**
 * 
 */
class AliyunOssDrivers implements \DdvPhp\DdvFile\Drivers\HandlerInterface
{
  private $client;
  private $config;
  private $getUrlByPathFn;
  public function __construct($config){
    $this->config = $config;
  }
  public function open(){
    try{
      $this->client = new OssClient($this->config['accessKeyId'], $this->config['accessKeySecret'], $this->config['endpoint'], $this->config['isCName'], $this->config['securityToken']);
    } catch(\OSS\Core\OssException $e) {
      throw new DriverException($e->getMessage(), 'ALIYUN_OSS_DRIVERS_OPEN_FAIL');
    }
  }
  public function close(){

  }
  public function setGetUrlByPath(\Closure $fn){
    $this->getUrlByPathFn = $fn;
  }
  /**
   * 文件path转url
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-10T19:37:20+0800
   * @param    [type]                   $path [description]
   * @return   [type]                        [description]
   */
  public function getUrlByPath($path){
    $fn = $this->getUrlByPathFn;
    if ($fn instanceof \Closure) {
      return $fn($path);
    }
    return 'http://pingqu-test.oss-cn-shenzhen.aliyuncs.com/'.$path;
  }
  /**
   * 获取上传id
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-10T20:29:23+0800
   * @param    string                  $path [description]
   * @return   [type]                        [description]
   */
  public function getUploadId($path){
    try{
      return $this->client->initiateMultipartUpload($this->config['bucket'], $this->getObjectKeyByPath($path));
    } catch(\OSS\Core\OssException $e) {
      throw new DriverException($e->getMessage(), 'GET_UPLOAD_ID');
    }
  }
  /**
   * 获取已经完成的分块
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-12T15:12:34+0800
   * @param    string                  $path [description]
   * @param    string                   $uploadId [description]
   * @return   array                              [description]
   */
  public function getUploadDoneParts($path, $uploadId){
    if (empty($uploadId)) {
      throw new DriverException("upload_id is empty", 'UPLOADID_MUST_HAS');
    }
    try {
      $listUploadInfo = $this->client->listParts($this->config['bucket'], $this->getObjectKeyByPath($path), $uploadId);
      $listParts = $listUploadInfo->getListPart();
      $uploadParts = array();
      foreach ($listParts as $part) {
        $uploadParts[] = intval($part->getPartNumber());
      }
      return $uploadParts;
    } catch(\OSS\Core\OssException $e) {
      throw new DriverException($e->getMessage(), 'GET_UPLOAD_DONE_PARTS');
    }
  }
  public function getUploadPartSign($path, $uploadId, $partNumber='1', $partLength=0, $md5Base64, $contentType='application/octet-stream'){
    return $this->getUploadPartSignRun($path, $uploadId, $partNumber, $md5Base64, $contentType);
  }
  private function getUploadPartSignRun($path, $uploadId, $partNumber='1', $md5Base64, $contentType='application/octet-stream', $method=OssClient::OSS_HTTP_PUT,$timeout = 60){

    if (empty($uploadId)) {
      throw new DriverException("upload_id is empty", 'UPLOADID_MUST_HAS');
    }
    if (empty($partNumber)) {
      throw new DriverException('part_number is empty', 'PART_NUMBER_MUST_HAS');
    }
    if (empty($md5Base64)) {
      throw new DriverException('md5_base64 is empty', 'MD5_BASE_MUST_HAS');
    }
    $upOptions = array(
      OssClient::OSS_UPLOAD_ID => $uploadId,
      OssClient::OSS_PART_NUM => $partNumber,
      OssClient::OSS_CONTENT_MD5 => $md5Base64,
      OssClient::OSS_CONTENT_TYPE => $contentType
    );

    $r = array();
    $r['url'] = $this->client->signUrl($this->config['bucket'], $this->getObjectKeyByPath($path), $timeout, $method, $upOptions);
    $r['method'] = $method;
    $r['headers'] = array();
    $r['headers']['Content-Type'] = $contentType;
    $r['headers']['Content-Md5'] = $md5Base64;
    $r['params'] = array();
    $r['params']['timeout'] = $timeout;
    unset($upOptions);
    return $r;
  }
  public function completeMultipartUpload($path, $uploadId){
    if (empty($uploadId)) {
      throw new DriverException("upload_id is empty", 'UPLOADID_MUST_HAS');
    }
    try {
      $listUploadInfo = $this->client->listParts($this->config['bucket'], $this->getObjectKeyByPath($path), $uploadId);
      $listParts = $listUploadInfo->getListPart();
      $uploadParts = array();
      foreach ($listParts as $part) {
        $uploadParts[] = array(
          'PartNumber'=>$part->getPartNumber(),
          'ETag'=>$part->getETag()
        );
      }
      return $this->client->completeMultipartUpload($this->config['bucket'], $this->getObjectKeyByPath($path), $uploadId, $uploadParts);
    } catch(\OSS\Core\OssException $e) {
      throw new DriverException($e->getMessage(), 'GET_UPLOAD_DONE_PARTS');
    }

  }
  public function getObjectKeyByPath($path){
    $objectKey = $path;
    if (substr($objectKey,0,1) === '/') {
      $objectKey = substr($objectKey,1);
    }
    return $objectKey;
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