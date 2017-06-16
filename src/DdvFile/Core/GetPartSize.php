<?php

namespace DdvPhp\DdvFile\Core;
use \DdvPhp\DdvFile\Exception\Input as InputException;


/**
 * Class GetPartSize
 *
 * Wrapper around PHPMaile
 *
 * @package DdvPhp\DdvFile\Core
 */
final class GetPartSize
{
  /**
   * @author 桦 <yuchonghua@163.com>
   * @DateTime 2016-04-12T15:23:14+0800
   * @param    [array]$data = array();
   * @return   [type]
   */
  public static function run(\Closure $call, \Closure $attr, array &$data){
    //判断设备类型
    if (empty($data['deviceType']) || !in_array(strtolower($data['deviceType']), $attr('sysDeviceType'))) {
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
    $r['partSize'] = ceil(min($data['fileSize'],max(ceil(($data['fileSize']/$attr('partSumMax'))),$attr('partSizeMin'))));
    //获取分块总数
    $r['partSum'] = ceil($data['fileSize']/$r['partSize']) ;
    //
    if($r['partSize'] > $attr('partSizeMax')){
      throw new InputException('文件太大','PART_SIZE_MORE_THAN');
    }
    if($r['partSum'] > $attr('partSumMax')){
      throw new InputException('块数目太大，不能超过'.$attr('partSumMax'),'PART_SUM_MORE_THAN');
    }
    return $r;
  }
}
?>
