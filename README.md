<p align="center">
  <img width="108px" src="https://yuntu-download.oss-cn-hangzhou.aliyuncs.com/GitResource/xlogo.jpg" />
</p>

<h1 align="center">DocSDK</h1>
<p align="center">English | <a href="doc/README-zh-CN.md">中文</a></p>

## About DocSDK

> DocSDK is a development kit for smart file conversion. We support the conversion of various types of documents, including pdf, doc, docx, xls, xlsx, ppt, pptx, dwg, caj, svg, html, json, png, jpg, gif and other formats, more conversion formats can be viewed on our [website](https://www.docsdk.com/). There are 8 kinds of SDK support, including Java, Node.js, PHP, Python, Swift, CLI, AWS-Lambda and Laravel.
> 
> **Keywords: document conversion, file conversion, PDF to Word, PDF to PPT, PDF to HTML**

## docsdk-php

> This is the official PHP SDK for the DocSDK API. 


### Install

To install the PHP SDK you will need to be using [Composer](https://getcomposer.org) in your project. 

Install the SDK alongside Guzzle 7:

```bash
composer require docsdk/docsdk-php php-http/guzzle7-adapter
```

This package is not tied to any specific HTTP client. Instead, it uses [Httplug](https://github.com/php-http/httplug) to let users choose whichever HTTP client they want to use.

If you want to use Guzzle 6 instead, use:

```bash
composer require docSDK/docSDK-php php-http/guzzle6-adapter
```


### Creating Jobs

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
            ->set('url','https://file-url')
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


### Uploading Files

Uploads to DocSDK are done via `import/upload` tasks. This SDK offers a convenient upload method:

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
The `upload()` method accepts a string, PHP resource or PSR-7 `StreamInterface` as second parameter.

You can also directly allow clients to upload files to DocSDK:

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


### Downloading Files

DocSDK can generate public URLs for using `export/url` tasks. You can use the PHP SDK to download the output files when the Job is finished.

```php
$docsdk->jobs()->wait($job); // Wait for job completion

foreach ($job->getExportUrls() as $file) {

    $source = $docsdk->getHttpTransport()->download($file->url)->detach();
    $dest = fopen('output/' . $file->filename, 'w');
    
    stream_copy_to_stream($source, $dest);

}
```

The `download()` method returns a PSR-7 `StreamInterface`, which can be used as a PHP resource using `detach()`.

### Resources
* [DocSDK API Documentation](https://www.docsdk.com/docAPI)
* [DocSDK home page](https://www.docsdk.com/)

### 关于 DocSDK
> DocSDK 是一个在线文件转换的开发工具包。我们支持各类文档的转换，其中包括 pdf、doc、docx、xls、xlsx、ppt、pptx、dwg、caj、svg、html、json、png、jpg 和 gif 等等各种格式的转换，更多转换格式可查看[网站](https://www.docsdk.com/) 。现有八种 SDK 的支持，其中包括 Java、Node.js、PHP、Python、Swift、CLI、AWS-Lambda 和 Laravel。
> 
> **关键词： 文档转换，文件转换，PDF转Word，PDF转PPT，PDF转HTML**
