# ddv-file

Installation - 安装
------------

```bash
composer require ddvphp/ddv-file
```

------

>* [客户端] 代指 **IOS** **安卓** **Html5** **flash**
>* [device_type] 目前允许 `ios` `android` `html5` `htmlswf` `html4` `wxmp`
>* 微信公众平台的浏览器一定要使用`wxmp`，不建议使用`html5`
>* 否则会导致部分手机上传失败，因为微信浏览器的限制导致分块的计算公式不一样

------

# 一、客户端使用
### 1、获取分块大小
>首先客户端[**IOS** **安卓** **Html5** **flash**]获取文件大小，
>调用一下接口，获取到分块大小以及总分块数

>**==服务户端计算规则==**
>定义 `$part_size_min = 400*1024`;
>定义 `$part_size_max = 15*1024*1024`;
>计算 `$part_size =  ceil(max($file_size/1000 , $part_size_min))`
>判断 `$part_size`如果大于`$part_size_max`抛出异常 暂时不能上传大于15G的文件
>计算 `$part_sum = ceil($file_size/$part_size)`
>判断 `$part_sum`如果大于`1000`抛出异常 可以等于 `1000` 暂时不能上传大于15G的文件
>`因为我们的服务器目前设定最大接受上传15G 另外分块太大也给移动端带来压力`
>`分块大小计算 是 取大原则` `向上去整`
>**==服务户端计算规则==**

###### 请求地址：**v1_0/upload/filePartSize**
###### 使用说明：获取分块大小
###### 需要登录：**是**
###### 请求方法：**GET**
###### 数据形式：**x-www-form-urlencoded** 、**form-data**

发送以下信息到服务器

属性 | 类型 | 是否必填 | 说明 
---|--- |--- |--- 
fileSize | int | 是 | 文件大小。
fileType | string | 是 | 可以参考**[Mime 类型列表][1]** 未知类型为 **application/octet-stream**
device_type | string | 是 | 设备类型 `ios` `android` `html5` `htmlswf` `html4` `wxmp`

样板数据

```json
{
  "fileSize"   :    "46650955",
  "fileType"   :    "audio/mp3",
  "deviceType" :    "html5"
}
```
返回结果

| 属性 | 类型 | 是否必填 | 说明 | 
| --- | --- | --- | --- |
| partSize | int | 是 | 切片大小。|
| partSum | int | 是 | 切片总块数。|

样板数据
```json
"partSize"      :"409600",
"partSum"           :114
```

### 2、获取文件id

> **前端读流方式计算文件的相关信息**
>*
>* 以**partSize**的大小进行流式读取文件
>* 计算文件的总内容的**md5**为**fileMd5**
>* 计算文件的总内容的**sha1**为**fileSha1**
>* 计算文件的总内容的**crc32**为**fileCrc32**
>* 计算当前**每一块**的流的**二进制**的**md5**的**hex值** 并且拼接为**partMd5Str**
>* 比如**第一块**的**md5**是 **0fdf5be93cd24aeeaccb046406c3a643**
>* 比如**第二块**的**md5**是 **986f5be93cd24ae9accb047776c3a332**
>* 比如**第三块**的**md5**是 **85658be93cd24aeeaccb046406c3a757**
>* 使用**partMd5Str**  = **partMd5Str** + **{上一块md5}**
>* 那么**partMd5Str**是**0fdf5be93cd24aeeaccb046406c3a643986f5be93cd24ae9accb047776c3a33285658be93cd24aeeaccb046406c3a757**
>* **partMd5Str**随着分块累计不停变长，但是**不会超过 1000*32=32000字节**
>* 最后 得到 **md5**、**sha1**、**crc32**
>* 还有 **filePartMd5Lower** = **md5(partMd5Str[转小写]) + '-' + partSum**
>* 还有 **filePartMd5Upper** = **md5(partMd5Str[转大写]) + '-' + partSum**
>* 得到 **filePartMd5Lower**为**4CF26963D7C141DEFBC985382538B43F-3**
>* 得到 **filePartMd5Upper**为**BB49107DFF0A0054DC67D94B1FFC5A24-3**


###### 请求地址：**v1_0/upload/fileId**
###### 使用说明：获取分块大小
###### 需要登录：**是**
###### 请求方法：**GET**
###### 数据形式：**x-www-form-urlencoded** 、**form-data**

发送以下信息到服务器

属性 | 类型 | 是否必填 | 说明 
---|--- |--- |--- 
fileMd5 | string | 是 | 文件md5[Hex][大写Hex结果]。
fileSha1 | string | 是 | 文件sha1[Hex][大写Hex结果]。
fileCrc32 | string | 是 | 文件crc32[Hex][大写Hex结果]。
filePartMd5Lower | string | 是 | 多块小写md5的md5[大写Hex结果]。
filePartMd5Upper | string | 是 | 多块大写md5的md5[大写Hex结果]。
fileName | string | 是 | 文件名称[带扩展名]。
fileSize | int | 是 | 文件大小[字节数]。
fileType | string | 是 | 可以参考**[Mime 类型列表][1]** 未知类型为 **application/octet-stream**
lastModified | int\|float | 是 | 文件最后修改时间[时间戳，到秒，可以带小数]。
manageType | string | 是 | 权限类型[**admin**\|**user**]。
directory | string | 是 | 目录[默认**common/other**]。
deviceType | string | 是 | 设备类型 `ios` `android` `html5` `htmlswf` `html4`
authType | string | 否 | .......具体看后台应用的业务需要

样板数据

```json
"fileCrc32"       :"5434bd00",
"fileMd5"         :"0fdf5be93cd24aeeaccb046406c3a643",
"fileSha1"          :"4b0e042ee37cc8947bd6e4a5ef6bbc53a85ba7f9",
"filePartMd5Lower"   :"4CF26963D7C141DEFBC985382538B43F-3",
"filePartMd5Upper"   :"BB49107DFF0A0054DC67D94B1FFC5A24-3",
"fileName"          :"1.mp3",
"fileSize"          :"46650955",
"fileType"          :"audio/mp3",
"lastModified"        :"1449682232.043",
"manageType"        :"admin",
"directory"         :"common/other",
"deviceType"        :"html5"
```
返回结果

| 属性 | 类型 | 是否必填 | 说明 | 
| --- | --- | --- | --- |
| fileId | string | 是 | 文件id。|
| fileCrc32 | string | 是 | 文件crc32。|
| fileMd5 | string | 是 | 文件md5。|
| fileSha1 | string | 是 | 文件sha1。|
| url | string | 是 | 文件如果成功上传后的url。|
| path | string | 是 | 文件如果成功上传后的path。|
| isUploadEnd | boolean | 是 | 是否已经成功上传。|

样板数据
```json
"fileId"          :"1",
"fileCrc32"         :"5434bd00",
"fileMd5"       :"0fdf5be93cd24aeeaccb046406c3a643",
"fileSha1"        :"4b0e042ee37cc8947bd6e4a5ef6bbc53a85ba7f9",
"path"            :"/fafssdf/df/as/fas/s.jpg",
"url"           :"http://www.xxxx.x.com/fafssdf/df/as/fas/s.jpg",
"isUploadEnd"           :false
```
>如果 `isUploadEnd` 已经成功上传就跳过下列所有步骤


### 3、获取成功上传的信息
###### 请求地址：**v1_0/upload/filePartInfo**
###### 使用说明：获取成功上传的信息
###### 需要登录：**是**
###### 请求方法：**GET**
###### 数据形式：**x-www-form-urlencoded** 、**form-data**

发送以下信息到服务器

| 属性 | 类型 | 是否必填 | 说明 |
| --- | --- | --- | --- |
| fileId | string | 是 | 文件id。|
| fileMd5 | string | 是 | 文件md5[Hex][大写Hex结果]。|
| fileSha1 | string | 是 | 文件sha1[Hex][大写Hex结果]。|
| fileCrc32 | string | 是 | 文件crc32[Hex][大写Hex结果]。|

样板数据

```json
"fileId"          :"1",
"fileCrc32"     :"5434bd00",
"fileMd5"       :"0fdf5be93cd24aeeaccb046406c3a643",
"fileSha1"        :"4b0e042ee37cc8947bd6e4a5ef6bbc53a85ba7f9"
```

服务器返回

| 属性 | 类型 | 是否必填 | 说明 | 
| --- | --- | --- | --- |
| fileSize | int | 是 | 文件大小。|
| partSize | int | 是 | 分块大小。|
| partSum | string | 是 | 总的分块个数。|
| doneParts | array | 是 | 成功上传的数组。|
| isUploadEnd | boolean | 是 | 是否已经成功上传。|

样板数据

```json

"fileSize"      :"46650955",
"partSize"      :"46651",
"partSum"           :1000,
"doneParts"         :[1,3,6,8],
"isUploadEnd"       :false

```

客户端从`1`开始循环到`{partSum}`切块 执行`4、获取分块签名`的步骤 跳过`doneParts`的成功上传块

>服务在这个时候建立uploadid 并且查询成功上传的块信息
>如果是第一次产生的uploadid 块信息doneParts为空数组
>如果有uploadid就查询阿里云或者百度云的done_parts信息

>**注意** 如果 `isUploadEnd`已经成功上传就跳过下列所有步骤


### 4、获取分块签名

###### 请求地址：**v1_0/upload/filePartMd5**
###### 使用说明：获取成功上传的信息
###### 需要登录：**是**
###### 请求方法：**GET**
###### 数据形式：**x-www-form-urlencoded** 、**form-data**

发送以下信息到服务器

| 属性 | 类型 | 是否必填 | 说明 |
| --- | --- | --- | --- |
| fileId | string | 是 | 文件id。 |
| fileMd5 | string | 是 | 文件md5[Hex][大写Hex结果]。 |
| fileSha1 | string | 是 | 文件sha1[Hex][大写Hex结果]。 |
| fileCrc32 | string | 是 | 文件crc32[Hex][大写Hex结果]。 |
| partNumber | int | 是 | 当前分块的序号，第几块。 |
| partLength | int | 是 | 当前分块的字节数，分块的大小或者最后一块大小。 |
| md5Base64 | string | 是 | 文件的md5的二进制值进行base64[参考这个说明][2]。 |
| deviceType | string | 是 | 设备类型[`ios` `android` `html5` `htmlswf` `html4`]。 |
| isHeaderArray | string | 否 | 是否传回数组头[默认:`flase`]。 |

样板数据

```json
"fileId"               :"1",
"fileCrc32"            :"5434bd00",
"fileMd5"              :"0fdf5be93cd24aeeaccb046406c3a643",
"fileSha1"             :"4b0e042ee37cc8947bd6e4a5ef6bbc53a85ba7f9",
"partNumber"           :"1",
"partLength"           :"30",
"md5Base64"            :"MDE1Mjg4ZDViMGFmZjBiYzExOTQ0NDhlODFmZDU1NTQ=",
"deviceType"           :"html5"

```

服务器返回

| 属性 | 类型 | 是否必填 | 说明 |
| --- | --- | --- | --- | 
| url | string | 是 | 分块数据请求发送地址。|
| method | string | 是 | 分块数据请求发送方式。|
| headers | array | 是 | 分块数据请求头。|

样板数据

```json
{
  "data":{
    "url":"http://xxxx.x.x.x.x.x.x/caomdsfas/fda/dfa/dsa",
    "method":"PUT",
    "headers":{
        "Content-Type":"application/octet-stream",
        "Content-Md5":"MDE1Mjg4ZDViMGFmZjBiYzExOTQ0NDhlODFmZDU1NTQ="
     },
    "headers_array":[
        ["Content-Type","application/octet-stream"],
        ["Content-Md5","MDE1Mjg4ZDViMGFmZjBiYzExOTQ0NDhlODFmZDU1NTQ="]
     ]
  }
}
```
> 因为头的key的特殊性
> 传参可以带 `isHeaderArray` = `true` 来得到数组头 

### 5、获取成功上传的信息

###### 请求地址：**v1_0/upload/complete**
###### 使用说明：获取成功上传的信息
###### 需要登录：**是**
###### 请求方法：**POST**
###### 数据形式：**x-www-form-urlencoded** 、**form-data**

发送以下信息到服务器

|属性 | 类型 | 是否必填 | 说明 |
| --- | --- | --- | --- | 
| fileId | string | 是 | 文件id。|
| fileMd5 | string | 是 | 文件md5[Hex][大写Hex结果]。|
| fileSha1 | string | 是 | 文件sha1[Hex][大写Hex结果]。|
| fileCrc32 | string | 是 | 文件crc32[Hex][大写Hex结果]。|

样板数据
```json
{
  "fileId"          :"1",
  "fileCrc32"       :"5434bd00",
  "fileMd5"         :"0fdf5be93cd24aeeaccb046406c3a643",
  "fileSha1"        :"4b0e042ee37cc8947bd6e4a5ef6bbc53a85ba7f9"
}

```
服务器返回有 空数组代表成功，如果抛出异常就看一下是否为漏切片

------

# 二、服务器使用

> 我们以`laravel`框架为样本使用

## 我们以laravel提供一个样本

>**==服务户端计算规则==**
> 定义 `$partSizeMin = 400*1024`;
> 定义 `$partSizeMax = 15*1024*1024`;
> 计算 `$partSize =  ceil(min(fileSize,max(ceil(fileSize/$partSumMax),$partSizeMin)))`
> 判断 `$partSize`如果大于`$partSizeMax`抛出异常 暂时不能上传大于15G的文件
> 计算 `$partSum = ceil($fileSize/$partSize)`
> 判断 `$partSum`如果大于`1000`抛出异常 可以等于 `1000` 暂时不能上传大于15G的文件
> `因为我们的服务器目前设定最大接受上传15G 另外分块太大也给移动端带来压力`
> `分块大小计算 是 取大原则` `向上去整`
>**==服务户端计算规则==**

### 1、路由初配置

```php


Route::group(['prefix'=>'upload'],function(){
    Route::get('filePartSize','Api\UploadController@filePartSize');
    Route::get('fileId','Api\UploadController@fileId');
    Route::get('filePartInfo','Api\UploadController@filePartInfo');
    Route::get('filePartMd5','Api\UploadController@filePartMd5');
    Route::post('complete','Api\UploadController@complete');
});

```

### 2、控制器文件`Api\UploadController`

#### 2.1、 配置初始化
```php

  public function __construct(){
    method_exists(parent::class, '__construct') && parent::__construct();
    $this->fileConfigInit();
  }
  private function fileConfigInit (){
    // 基本配置
    $config = [
      // uid 可以为null和字符串0
      // uid 如果为null 每次获取fileId都会拿到新的id，因为每个文件不确定是那个用户的
      // uid 如果为0 ，插件支持0这个uid，没有登录的用户统一视为0这个用户的
      'uid'=>'0'
      // fileIndex 是一个可选参数，如果配置了，会导致文件系统返回的地址通通是索引地址哦
      // fileIndex 是一个索引标识
      'fileIndex'=>'videolive',
      // 默认不属于uid标识索引文件
      'fileIndexUseUid'=>false,
      // 默认一块最小字节数
      'partSizeMin'=>400*1024,
      // 默认一块最大字节数
      'partSizeMax'=>15*1024*1024,
      // 默认一共可以多少块
      'partSumMax'=>1000
    ];
    // 使用存储驱动，比如阿里云的驱动，也可以pr扩展驱动[感谢]
    $drivers = new \DdvPhp\DdvFile\Drivers\AliyunOssDrivers(config('aliyun.oss'));
    // 数据库模型，目前提供laravel数据模型，也可以pr数据模型[感谢]
    $database = new \DdvPhp\DdvFile\Database\LaravelMysqlDatabase();
    // 实例化文件类
    $this->upload = new \DdvPhp\DdvFile($config, $drivers, $database);
  }

```

#### 2.2、获取分块大小 接口

```php

  ## 获取分块大小
  public function filePartSize (Request $request){
    return [
      'data' =>
        $this->upload->getPartSize($request->only(['fileSize','fileType','deviceType']))
    ];
  }

```

#### 2.3、获取文件id 接口

```php


  ## 获取文件id
  public function fileId(Request $request){
    $input = $request->only(
      $this->upload->getFileIdInputKeys([
        // 授权类型
        'authType',
        // 管理类型
        'manageType',
        // 上传目录
        'directory'
      ])
    );
    // 自己的业务逻辑,和权限逻辑
    return [
      'data' =>
        $this->upload->getFileId($input)
    ];
  }

```

#### 2.3、获取成功上传的信息

```php

  ## 获取成功上传的信息
  public function filePartInfo(Request $request){
    $input = $request->only(
      [
        'fileId',
        'fileMd5',
        'fileSha1',
        'fileCrc32'
      ]
    );
    return [
      'data' =>
        $this->upload->getFilePartInfo($input)
    ];
  }

```

#### 2.4、获取分块签名

```php

  ## 获取分块签名
  public function filePartMd5 (Request $request){
    $input = $request->only(
      [
        'fileId',
        'fileMd5',
        'fileSha1',
        'fileCrc32',


        'contentMd5',
        'partLength',
        'partNumber'
      ]
    );
    return [
      'data' =>
        $this->upload->getFilePartMd5($input)
    ];

  }

```

#### 2.5、合并上传文件

```php

  ## 合并上传文件
  public function complete(Request $request){
    $input = $request->only(
      [
        'fileId',
        'fileMd5',
        'fileSha1',
        'fileCrc32'
      ]
    );
    return [
      'data' =>
        $this->upload->complete($input)
    ];
  }

```






[1]: http://www.w3school.com.cn/media/media_mimeref.asp
[2]: http://www.ituring.com.cn/article/74167