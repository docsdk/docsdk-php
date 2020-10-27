<?php


namespace DocSDK\Tests\Integration;


use DocSDK\Models\ConvertTask;
use DocSDK\Models\ExportUrlTask;
use DocSDK\Models\ImportUploadTask;
use DocSDK\Models\ImportUrlTask;
use DocSDK\Models\Job;
use DocSDK\Models\Task;

class JobTest extends TestCase
{

    public function testCreateJob()
    {

        $job = (new Job())
            ->setTag('integration-test-create-job')
            ->addTask(
                (new Task('import/url', 'import-it'))
                    ->set('url', 'http://invalid.url')
                    ->set('filename', 'test.file')
            )
            ->addTask(
                (new Task('convert', 'convert-it'))
                    ->set('input', ['import-it'])
                    ->set('output_format', 'pdf')
            );

        $this->docSDK->jobs()->create($job);

        $this->assertNotNull($job->getId());
        $this->assertEquals('integration-test-create-job', $job->getTag());
        $this->assertNotNull($job->getCreatedAt());
        $this->assertCount(2, $job->getTasks());

        $task1 = $job->getTasks()->whereOperation('convert')[0];
        $task2 = $job->getTasks()->whereOperation('import/url')[0];

        $this->assertEquals('convert-it', $task1->getName());

        $this->assertEquals('import-it', $task2->getName());

    }


    public function testUploadAndDownloadFiles()
    {

        $job = (new Job())
            ->setTag('integration-test-upload-download')
            ->addTask(
                new Task('import/upload', 'import-it')
            )
            ->addTask(
                (new Task('export/url', 'export-it'))
                    ->set('input', ['import-it'])
            );

        $this->docSDK->jobs()->create($job);

        $uploadTask = $job->getTasks()->whereName('import-it')[0];

        $this->docSDK->tasks()->upload($uploadTask, fopen(__DIR__ . '/files/input.pdf', 'r'));

        $this->docSDK->jobs()->wait($job);
        $this->assertEquals(Job::STATUS_FINISHED, $job->getStatus());

        $exportTask = $job->getTasks()->whereStatus(Task::STATUS_FINISHED)->whereName('export-it')[0];

        $this->assertNotNull($exportTask->getResult());

        $file = $job->getExportUrls()[0];

        $this->assertNotEmpty($file->url);

        $source = $this->docSDK->getHttpTransport()->download($file->url)->detach();

        $dest = tmpfile();
        $destPath = stream_get_meta_data($dest)['uri'];

        stream_copy_to_stream($source, $dest);


        $this->assertEquals(filesize($destPath), 172570);


    }


}
