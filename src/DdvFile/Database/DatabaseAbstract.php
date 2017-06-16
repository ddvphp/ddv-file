<?php
namespace DdvPhp\DdvFile\Database;

/**
 * 
 */
abstract class DatabaseAbstract implements HandlerInterface
{
  public function __construct(){
    $this->dbKeyToCalssKeyInit();
  }
  public function dbKeyToCalssKeyInit(){
    foreach ($this->calssKeyToDbKey as $value => $key) {
      if(empty($dbKeyToCalssKey[$key])){
        $dbKeyToCalssKey[$key] = $value;
      }
    }
  }
  public $dbKeyToCalssKey = array();
  public $calssKeyToDbKey = array(
    // 分块大小
    'partSize'=>'part_size',
    // 分块总数
    'partSum'=>'part_sum',
    // 扩展名
    'fileExtName'=>'ext_name',
    // 文件名
    'fileName'=>'name',
    // 文件大小
    'fileSize'=>'size',
    // 文件md5
    'fileMd5'=>'md5',
    // 文件sha1
    'fileSha1'=>'sha1',
    // 文件crc32
    'fileCrc32'=>'crc32',
    // 文件类型
    'fileType'=>'type',
    // 文件path
    'filePath'=>'path',
    // 文件块小写md5集合的md5
    'filePartMd5Lower'=>'part_md5_lower',
    // 文件块大写md5集合的md5
    'filePartMd5Upper'=>'part_md5_upper',
    // 文件最后修改时间
    'lastModified'=>'last_modified',
    // 创建时间
    'createTime'=>'create_time',
  );
}