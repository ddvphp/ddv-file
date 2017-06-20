<?php

namespace DdvPhp;
use DdvPhp\DdvFile\Core as DdvCore;

/**
 * Class DdvFile
 *
 * @package DdvPhp\DdvFile
 */
class DdvFile
{
  /**
   * [第一步]获取文件分块建议大小
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-10T16:29:07+0800
   * @param    array                    $data [description]
   * @return   [type]                         [description]
   */
  public function getPartSize(array $data=array()){
    return DdvCore\GetPartSize::run($this->call, $this->attr, $data);
  }
  /**
   * [第二步]获取文件上传fileId
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-10T16:28:25+0800
   * @param    array                    $data [description]
   * @return   [type]                         [description]
   */
  public function getFileId(array $data=array()){
    return DdvCore\GetFileId::run($this->call, $this->attr, $data);
  }
  /**
   * [第三步]获取文件块信息
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-10T20:16:18+0800
   * @param    array                    $data [description]
   * @return   [type]                         [description]
   */
  public function getFilePartInfo(array $data=array()){
    return DdvCore\GetFilePartInfo::run($this->call, $this->attr, $data);
  }
  /**
   * [第四步]获取每块md5
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-12T15:48:21+0800
   * @param    array                    $data [description]
   * @return   [type]                         [description]
   */
  public function getFilePartMd5(array $data=array()){
    return DdvCore\GetFilePartMd5::run($this->call, $this->attr, $data);
  }
  /**
   * 合并文件
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-12T16:09:13+0800
   * @param    array                    $data [description]
   * @return   [type]                         [description]
   */
  public function complete(array $data=array()){
    return DdvCore\Complete::run($this->call, $this->attr, $data);
  }
  public function getUrlByPath($path){
      return DdvCore\FileInfo::getUrlByPath($this->call, $this->attr, $path);
  }
  /**
   * [增强方法]获取文件信息
   * @author: 林跃 <@qq.com>
   * @DateTime 2017-06-10T16:28:25+0800
   * @param    array                    $data [description]
   * @return   [type]                         [description]
   */
  public function getFileInfo($fileId){
    return DdvCore\FileInfo::getFileInfo($this->call, $this->attr, $fileId);
  }
  /**
   * [增强方法]更新文件信息
   * @author: 林跃 <@qq.com>
   * @DateTime 2017-06-10T16:28:25+0800
   * @param    array                    $data [description]
   * @return   [type]                         [description]
   */
  public function updateFileInfo($fileId, array $data=array()){
    return DdvCore\FileInfo::updateFileInfo($this->call, $this->attr, $fileId, $data);
  }
  public function getFileIdInputKeys(array $extendKeys=array()){
    return array_merge($this->sysGetFileIdInputKeys, $extendKeys);
  }
  public function __construct(
    $config = null,
    // 存储驱动
    \DdvPhp\DdvFile\Drivers\HandlerInterface $driver,
    // 数据库模型
    \DdvPhp\DdvFile\Database\HandlerInterface $database
  ){
    // 存储驱动
    $this->driver = $driver;
    // 打开驱动
    $this->driver->open();
    // 数据库模型
    $this->database = $database;
    // 打开数据库模型
    $this->database->open();
    // 属性代理初始化
    $this->attrProxyInit();
    // 调用代理初始化
    $this->callProxyInit();
    // 配置初始化
    $this->configBaseInit(is_array($config) ? $config : array());
  }
  //配置信息初始化
  protected function attrProxyInit(){
    $this->attr = function &($name) {
      $num = func_num_args();
      $name = $num > 0 ? func_get_arg(0) : null;
      if ($num===1) {
        return $this->$name;
      }elseif ($num===2) {
        $this->$name = func_get_arg(1);
        return $this->$name;
      }else{
        return $this;
      }
    };
  }
  //配置信息初始化
  protected function callProxyInit(){
    $this->call = function ($name) {
      $num = func_num_args();
      if ($num>0) {
        $args = func_get_args();
        $name = $args[0];
        $args = $num>1 ? array_slice($args, 1) : array() ;
        return call_user_func_array(array($this, $name), $args);
      }else{
        throw new \DdvPhp\DdvFile\Exception\Sys("call args error", 'CALL_ARGS_ERROR');
      }
    };
  }
  //配置信息初始化
  protected function configBaseInit(array $config=array()){
    //默认一块最小字节数
    $this->partSizeMin = isset($config['partSizeMin']) ? intval($config['partSizeMin']) : (400*1024);
    //默认一块最大字节数
    $this->partSizeMax = isset($config['partSizeMax']) ? intval($config['partSizeMax']) : (15*1024*1024);
    //默认一共可以多少块
    $this->partSumMax = isset($config['partSumMax']) ? intval($config['partSumMax']) : (1000);
    //设置用户uid
    $this->uid = isset($config['uid']) ? $config['uid'] : $this->uid;

  }
  // 是否使用uid
  protected $uid = null;
  // 属性代理
  protected $attr;
  // 调用代理
  protected $call;
  // 存储驱动
  protected $driver;
  // 数据库模型
  protected $database;
  // 默认一块最小字节数
  protected $partSizeMin = 0;
  // 默认一块最大字节数
  protected $partSizeMax = 0;
  // 默认一共可以多少块
  protected $partSumMax = 0;
  // 允许设备
  protected $sysDeviceType = array('ios','android','html5','htmlswf','html4','wxmp');
  // 默认需要的key
  protected $sysGetFileIdInputKeys = array(
    // 文件名字
    'fileName',
    // 文件大小
    'fileSize',
    // 文件类型
    'fileType',
    // 文件签名-crc32
    'fileCrc32',
    // 文件签名-md5
    'fileMd5',
    // 文件签名-sha1
    'fileSha1',
    // 文件块小写md5集合的md5
    'filePartMd5Lower',
    // 文件块大写md5集合的md5
    'filePartMd5Upper',
    // 文件最后修改时间
    'lastModified',
    // 设备类型
    'deviceType'
  );

}
