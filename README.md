docsdk-php
=======================

> This is the official PHP SDK for the DocSDK API. 


Install
-------------------

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


Creating Jobs
-------------------
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


Uploading Files
-------------------
Uploads to DocSDK are done via `import/upload` tasks. This SDK offers a convenient upload method:

```php
use \DocSDK\Models\Job;
use \DocSDK\Models\ImportUploadTask;


$job = (new Job())
    ->addTask(new Task('import/upload','upload-my-file'))
    ->addTask(
        (new Task('convert', 'convert-my-file'))
            ->set('input', 'import-my-file')
            ->set('output_format', 'pdf')
    )
    ->addTask(
        (new Task('export/url', 'export-my-file'))
            ->set('input', 'convert-my-file')
    );

$docsdk->jobs()->create($job);

$uploadTask = $job->getTasks()->whereName('upload-my-file')[0];

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


Downloading Files
-------------------

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
