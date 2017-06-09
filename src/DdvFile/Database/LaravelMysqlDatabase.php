<?php
namespace DdvPhp\DdvFile\Database;

/**
 * 
 */
class LaravelMysqlDatabase implements \DdvPhp\DdvFile\Database\HandlerInterface
{
  public function open($driverConfig){

  }
  public function close(){

  }
  /**
   * 通过索引url查询文件id
   * @author: 林跃 <769353695@qq.com>
   * @DateTime 2017-06-06T13:40:30+0800
   * @param    string                   $url      [必填，索引url]
   * @return   string                             [文件id]
   */
  public function getFileIdByUrl($url){
  }
  /**
   * 通过索引url查询文件信息
   * @author: 林跃 <769353695@qq.com>
   * @DateTime 2017-06-06T13:42:18+0800
   * @param    string                   $fileID   [必填，文件id]
   * @return   Array                              [文件信息]
   */
  public function getFileInfoByFileID($fileID){
  }
  /**
   * 通过索引url查询文件源url
   * @author: 林跃 <769353695@qq.com>
   * @DateTime 2017-06-06T13:42:46+0800
   * @param    string                   $url      [必填，索引url]
   * @return   string                             [源文件url]
   */
  public function getSourceUrlByUrl($url){
  }
  /**
   * 通过crc32、sha1、md5、uid查询文件id
   * @author: 林跃 <769353695@qq.com>
   * @DateTime 2017-06-06T13:43:15+0800
   * @param    string                   $crc32    [必填，文件crc32]
   * @param    string                   $sha1     [必填，文件sha1]
   * @param    string                   $md5      [必填，文件md5]
   * @param    string                   $uid      [必填，用户id]
   * @return   string                             [文件id]
   */
  public function getFileIdByCrc32Sha1Md5Uid($crc32, $sha1, $md5, $uid){
  }
  /**
   * 查询文件列表,可以指定uid
   * @author: 林跃 <769353695@qq.com>
   * @DateTime 2017-06-06T13:44:08+0800
   * @param    string                   $pageNow  [必填，当前页]
   * @param    string                   $pageSize [必填，每页数]
   * @param    string                   $uid      [可选，用户uid]
   * @return   [type]                             [description]
   */
  public function getLists($pageNow, $pageSize, $uid = null){
  }
}