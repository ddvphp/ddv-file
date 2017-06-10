<?php
namespace DdvPhp\DdvFile\Drivers;

/**
 * 
 */
interface HandlerInterface
{
  public function open($driverConfig);
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
}