<p align="center">
  <img width="108px" src="https://yuntu-images.oss-cn-hangzhou.aliyuncs.com/xlogo.jpg" />
</p>

<h1 align="center">DocSDK</h1>
<p align="center"><a href="/README.md">English</a> | 中文</p>

## 关于 DocSDK
> DocSDK 是一个在线文件转换的开发工具包。我们支持各类文档的转换，其中包括 pdf、doc、docx、xls、xlsx、ppt、pptx、dwg、caj、svg、html、json、png、jpg 和 gif 等等各种格式的转换，更多转换格式可查看[网站](https://www.docsdk.com/) 。现有八种 SDK 的支持，其中包括 Java、Node.js、PHP、Python、Swift、CLI、AWS-Lambda 和 Laravel。
> 
> **关键词： 文档转换，文件转换，PDF转Word，PDF转PPT，PDF转HTML**


## docsdk-php

> 这是 [DocSDK API](https://www.docsdk.com/docAPI#sdk) 官方的 PHP 开发工具包.


### 安装

要安装 PHP SDK，您需要在项目中使用 [Composer](https://getcomposer.org)。 

与 Guzzle 7 一起安装 SDK：

```bash
composer require docsdk/docsdk-php php-http/guzzle7-adapter
```

这个包没有绑定到任何特定的 HTTP 客户端。实际上，它使用 [Httplug](https://github.com/php-http/httplug) 让用户选择他们想要使用的 HTTP 客户端。

如果您想改用 Guzzle 6，请使用：

```bash
composer require docSDK/docSDK-php php-http/guzzle6-adapter
```

### 创建 Jobs

```php
use \DocSDK\DocSDK;
use \DocSDK\Models\Job;
use \DocSDK\Models\Task;


$docsdk = new DocSDK([
    'api_key' => 'API_KEY',
    'sandbox' => false
]);


$job = (new Job())
    ->setTag('myjob-1')
    ->addTask(
        (new Task('import/url', 'ImportURL'))
            ->set('url','https://my-url')
    )
    ->addTask(
        (new Task('convert', 'ConvertFile'))
            ->set('input', 'ImportURL')
            ->set('output_format', 'pdf')
    )
    ->addTask(
        (new Task('export/url', 'ExportResult'))
            ->set('input', 'ConvertFile')
    );

$docsdk->jobs()->create($job)

```


### 上传文件

可通过 `import/upload` 上传文件到 DocSDK。以下是一种简单的上传方法：

```php
use \DocSDK\Models\Job;
use \DocSDK\Models\ImportUploadTask;


$job = (new Job())
    ->addTask(new Task('import/upload','UploadFile'))
    ->addTask(
        (new Task('convert', 'ConvertFile'))
            ->set('input', 'ImportFile')
            ->set('output_format', 'pdf')
    )
    ->addTask(
        (new Task('export/url', 'ExportResult'))
            ->set('input', 'ConvertFile')
    );

$docsdk->jobs()->create($job);

$uploadTask = $job->getTasks()->whereName('UploadFile')[0];

$docsdk->tasks()->upload($uploadTask, fopen('./file.pdf', 'r'));
```
`upload()` 方法接受一个字符串、PHP 资源或 PSR-7 `StreamInterface` 作为第二个参数。

也可以直接允许客户端上传文件到 DocSDK：

```html
<form action="<?=$uploadTask->getResult()->form->url?>"
      method="POST"
      enctype="multipart/form-data">
    <? foreach ((array)$uploadTask->getResult()->form->parameters as $parameter => $value) { ?>
        <input type="hidden" name="<?=$parameter?>" value="<?=$value?>">
    <? } ?>
    <input type="file" name="file">
    <input type="submit">
</form>
```


### 下载文件

DocSDK 可以使用 `export/url` 生成公开的链接，您可以使用这些 URL 下载输出文件。

```php
$docsdk->jobs()->wait($job); // Wait for job completion

foreach ($job->getExportUrls() as $file) {

    $source = $docsdk->getHttpTransport()->download($file->url)->detach();
    $dest = fopen('output/' . $file->filename, 'w');
    
    stream_copy_to_stream($source, $dest);

}
```

`download()` 方法返回一个 PSR-7 `StreamInterface`，可以使用 `detach()` 将其用作 PHP 资源。

### 参考资源
* [DocSDK API 文档](https://www.docsdk.com/docAPI)
* [DocSDK 主页](https://www.docsdk.com/)
