<?php
namespace DdvPhp\DdvFile\Database;

use \DdvPhp\DdvFile\Exception\Database as DatabaseException;

/**
 * 
 */
class CodeIgniterDatabase extends DatabaseAbstract
{
  private $db;
  private $table;
  public function __construct($db, $table){
    $this->db = &$db;
    $this->table = $table;
  }
  public function open(){
  }
  public function close(){

  }
  /**
   * 通过url查询文件id
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-06T13:40:30+0800
   * @param    string                   $url      [必填，索引url]
   * @return   string                             [文件id]
   */
  public function getFileIdByIndexUrl($url){

  }
  /**
   * 通过索引url查询文件信息
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-06T13:42:18+0800
   * @param    string                   $id   [必填，文件id]
   * @return   Array                              [文件信息]
   */
  public function getFileInfoByFileID($id){
    try {
      $res = $this->db->select('*')->from($this->table)->where('id',$id)->get()->row_array();

      if (empty($res)) {
        throw new DatabaseException('file not find', 'GET_FILE_ID_FAIL');
      }else{
        return $res;
      }
    } catch (Exception $e) {
      throw new DatabaseException($e->getMessage(), 'GET_FILE_ID_FAIL');
    }
  }
  /**
   * 通过索引url查询文件源url
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-06T13:42:46+0800
   * @param    string                   $url      [必填，索引url]
   * @return   string                             [源文件url]
   */
  public function getSourceUrlByIndexUrl($url){
  }
  /**
   * 通过crc32、sha1、md5、uid查询文件id
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-06T13:43:15+0800
   * @param    string                   $md5      [必填，文件md5]
   * @param    string                   $sha1     [必填，文件sha1]
   * @param    string                   $crc32    [必填，文件crc32]
   * @param    string                   $uid      [必填，用户id]
   * @return   string                             [文件id]
   */
  public function getFileIdByCrc32Sha1Md5Uid($md5, $sha1, $crc32, $uid){
    try {
      $res = $this->db->select('id')
            ->from($this->table)
            ->where('uid',$uid)
            ->where('md5',$md5)
            ->where('sha1',$sha1)
            ->where('crc32',$crc32)
            ->get()
            ->row_array();
      if (empty($res)) {
        throw new DatabaseException('file not find', 'GET_FILE_ID_FAIL');
      }else{
        return (string)$res['id'];
      }
    } catch (Exception $e) {
      throw new DatabaseException($e->getMessage(), 'GET_FILE_ID_FAIL');
    }
  }
  /**
   * 查询文件列表,可以指定uid
   * @author: 桦 <yuchonghua@163.com>
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
   * [uid,id,part_md5_lower,part_md5_upper,type,name,last_modified,status]
   */
  public function getListsByCrc32Sha1Md5($offset = 0, $size = 10, $md5, $sha1, $crc32, $status = null){
    try {
      return $this->db->select(array(
        'uid',
        'id',
        'part_md5_lower',
        'part_md5_upper',
        'type',
        'name',
        'last_modified',
        'status'
      ))
      ->from($this->table)
      ->where('md5',$md5)
      ->where('sha1',$sha1)
      ->where('crc32',$crc32)
      ->limit($size, $offset)
      ->get()
      ->result_array();
    } catch (Exception $e) {
      throw new DatabaseException($e->getMessage(), 'INSERT_FILE_INFO_FAIL');
    }
  }
  /**
   * 更新文件数据库，通过指定fileId更新data
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-10T20:45:12+0800
   * @param    [type]                   $id   [description]
   * @param    [type]                   $data [description]
   * @return   [type]                         [description]
   */
  public function updateFileInfoByFileID($id,array $data){
    try {
      return $this->db->where('id',$id)->update($this->table,$data);
    } catch (Exception $e) {
      throw new DatabaseException($e->getMessage(), 'INSERT_FILE_INFO_FAIL');
    }
  }
  /**
   * 插入文件表
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-12T12:06:32+0800
   * @param    array                    $data     [插入数据库的信息]
   * @return   string                   $fileId   [返回文件id]
   */
  public function insertFileInfo(array $data){
    try {
      $data['create_time'] = time();
      $this->db->insert($this->table, $data);
      return $this->db->insert_id();
    } catch (Exception $e) {
      throw new DatabaseException($e->getMessage(), 'INSERT_FILE_INFO_FAIL');
    }
  }
}