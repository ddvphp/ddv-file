<?php

namespace DdvPhp\DdvFile;
use const null;
// use Closure;
use \DdvPhp\DdvFile\Exception\Input as InputException;

/**
 * Class DdvFile
 *
 * Wrapper around PHPMaile
 *
 * @package DdvPhp\DdvFile\DdvFileBase
 */
class DdvFileBase
{
  // 默认一块最小字节数
  protected $partSizeMin = 0;
  // 默认一块最大字节数
  protected $partSizeMax = 0;
  // 默认一共可以多少块
  protected $partSumMax = 0;
  // 允许设备
  protected $deviceType = array('ios','android','html5','htmlswf','html4','wxmp');
  // 允许目录
  protected $directort=array('');
  public function __construct($config = null)
  {
    $this->configBaseInit(is_array($config) ? $config : array());
  }

  //配置信息初始化
  public function configBaseInit(array $config=array()){
    //默认一块最小字节数
    $this->partSizeMin = isset($config['partSizeMin'])?intval($config['partSizeMin']):(400*1024);
    //默认一块最大字节数
    $this->partSizeMax = isset($config['partSizeMax'])?intval($config['partSizeMax']):(15*1024*1024);
    //默认一共可以多少块
    $this->partSumMax = isset($config['partSumMax'])?intval($config['partSumMax']):(1000);

  }
  /**
   * @author 桦 <yuchonghua@163.com>
   * @DateTime 2016-04-12T15:23:14+0800
   * @param    [array]$data = array();
   * @return   [type]
   */
  public function getPartSize(array $data=array()){
    //判断设备类型
    if (empty($data['deviceType']) || !in_array(strtolower($data['deviceType']), $this->deviceType)) {
      throw new InputException('deviceType 不能为空','DEVICE_TYPE_NOT_CAN_EMPTY');
    }
    //默认文件类型
    $data['fileType'] = empty($data['fileType'])?'application/octet-stream':$data['fileType'];
    //强制fileSize是数字
    $data['fileSize'] = isset($data['fileSize'])?intval($data['fileSize']):0;
    //文件大小不能为0
    if ($data['fileSize']<=0) {
      throw new InputException('文件太小了','FILE_SIZE_TOO_SMALL');
    }
    $r = array();
    //获取分块大小
    $r['partSize'] = ceil(min($data['fileSize'],max(ceil(($data['fileSize']/$this->partSumMax)),$this->partSizeMin)));
    //获取分块总数
    $r['partSum'] = ceil($data['fileSize']/$r['partSize']) ;
    //
    if($r['partSize']>$this->partSizeMax){
      throw new InputException('文件太大','PART_SIZE_MORE_THAN');
    }
    if($r['partSum']>$this->partSumMax){
      throw new InputException('块数目太大，不能超过'.$this->partSumMax,'PART_SUM_MORE_THAN');
    }
    return $r;
  }
  //[第二步]获取文件上传file_id
  /**
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2016-04-12T17:39:34+0800
   * @param    [array]$data
   * @return   [type]
   */
  public function getFileId(array $data=array()){
    //多维数据检测
    if (isset($data[0])) {
      $r=array();
      foreach ($data as $key => $file) {
        $rdata = array();
        try {
          $rdata=$this->getFileIdOne($file);
        } catch (\DdvPhp\DdvException\Error $e) {
          $rdata['statusCode'] = method_exists($e,'getCode') ? $e->getCode() : 500;
          $rdata['error_id'] = method_exists($e,'getErrorId') ? $e->getErrorId() : 'UNKNOWN_ERROR';
          $rdata['msg'] = $e->getMessage();
          $rdata['msg'] = empty($rdata['msg'])?'':$rdata['msg'];
        }
        $r[] = $rdata;
      }
    }else{
      $r = $this->getFileIdOne($data);
    }
    return $r;
  }
  /**
   * [getFileIdOne 获取一个文件的上传id]
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2016-04-15T14:10:25+0800
   * @param    array                    $data [description]
   * @return   [type]                         [description]
   */
  protected function getFileIdOneCheckInputData(array &$data){
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
    if (empty($data['deviceType']) || !in_array(strtolower($data['deviceType']), $this->deviceType)) {
      throw new InputException('暂时不支持该设备类型','DEVICE_TYPE_ERROR');
    }
  }
  
}
?>
