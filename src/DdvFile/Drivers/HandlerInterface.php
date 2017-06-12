<?php
namespace DdvPhp\DdvFile\Drivers;

/**
 * 
 */
interface HandlerInterface
{
  public function open();
  public function close();
  /**
   * 文件path转url
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-10T19:37:20+0800
   * @param    [type]                   $url [description]
   * @return   [type]                        [description]
   */
  public function getUrlByPath($url);
  /**
   * 
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-10T20:29:23+0800
   * @param    string                  $path [description]
   * @return   [type]                        [description]
   */
  public function getUploadId($path);
  /**
   * 获取已经完成的分块
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-12T15:12:34+0800
   * @param    string                  $path [description]
   * @param    string                   $uploadId [description]
   * @return   array                              [description]
   */
  public function getUploadDoneParts($path, $uploadId);
  /**
   * [getUploadPartSign description]
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-12T16:22:44+0800
   * @param    [type]                   $path          [description]
   * @param    [type]                   $uploadId      [description]
   * @param    string                   $partNumber    [description]
   * @param    integer                  $partLength    [description]
   * @param    [type]                   $md5Base64     [description]
   * @param    string                   $contentType   [description]
   * @param    boolean                  $isHeaderArray [description]
   * @return   [type]                                  [description]
   */
  public function getUploadPartSign($path, $uploadId, $partNumber='1', $partLength=0, $md5Base64, $contentType='application/octet-stream');
  /**
   * 合并
   * @author: 桦 <yuchonghua@163.com>
   * @DateTime 2017-06-12T16:23:07+0800
   * @param    [type]                   $path     [description]
   * @param    [type]                   $uploadId [description]
   * @return   [type]                             [description]
   */
  public function completeMultipartUpload($path, $uploadId);
}