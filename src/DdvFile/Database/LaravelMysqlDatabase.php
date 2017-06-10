<?php
namespace DdvPhp\DdvFile\Database;

/**
 * 
 */
class LaravelMysqlDatabase implements \DdvPhp\DdvFile\Database\HandlerInterface
{
  public function open($config){

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
   * @param    string                   $id   [必填，文件id]
   * @return   Array                              [文件信息]
   */
  public function getFileInfoByFileID($id){
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
   * @param    string                   $md5      [必填，文件md5]
   * @param    string                   $sha1     [必填，文件sha1]
   * @param    string                   $crc32    [必填，文件crc32]
   * @param    string                   $uid      [必填，用户id]
   * @return   string                             [文件id]
   */
  public function getFileIdByCrc32Sha1Md5Uid($md5, $sha1, $crc32, $uid){
  }
  /**
   * 查询文件列表,可以指定uid
   * @author: 林跃 <769353695@qq.com>
   * @DateTime 2017-06-06T13:44:08+0800
   * @param    string                   $pageNow  [必填，当前页]
   * @param    string                   $pageSize [必填，每页数]
   * @param    string                   $uid      [可选，用户uid]
   * @return   Array                              [二维数组]
   */
  public function getLists($pageNow, $pageSize, $uid = null){
  }
  /**
   * 查询文件列表,通过crc32、sha1、md5
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-10T16:36:52+0800
   * @param    int                      $offset   [必填，偏移]
   * @param    int                      $size     [必填，数量]
   * @param    string                   $md5      [必填，文件md5]
   * @param    string                   $sha1     [必填，文件sha1]
   * @param    string                   $crc32    [必填，文件crc32]
   * @param    null|string              $status   [默认null, null全部状态, 如果指定就查某一个状态]
   * @return   Array                              [二维数组]
   * [uid,id,partMd5Lower,partMd5Upper,type,name,lastModified,status]
   */
  public function getListsByCrc32Sha1Md5($offset, $size, $md5, $sha1, $crc32, $status = null){

  }
  /**
   * 更新文件数据库，通过指定fileId更新data
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-10T20:45:12+0800
   * @param    [type]                   $id   [description]
   * @param    [type]                   $data [description]
   * @return   [type]                         [description]
   */
  public function updateFileInfoByFileID($id, $data){

  }
}