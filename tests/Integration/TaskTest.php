<?php


namespace DocSDK\Tests\Integration;


use DocSDK\Models\ImportUploadTask;
use DocSDK\Models\ImportUrlTask;
use DocSDK\Models\Task;

class TaskTest extends TestCase
{

    public function testCreateImportUrlTask()
    {

        $task = (new Task('import/url', 'url-test'))
            ->set('url', 'http://invalid.url')
            ->set('filename', 'test.file');

        $this->docSDK->tasks()->create($task);

        $this->assertNotNull($task->getId());
        $this->assertNotNull($task->getCreatedAt());
        $this->assertEquals('import/url', $task->getOperation());
        $this->assertEquals([
            'url'      => 'http://invalid.url',
            'filename' => 'test.file'
        ], (array)$task->getPayload());
        $this->assertEquals(Task::STATUS_WATING, $task->getStatus());


    }


    public function testUploadFile()
    {

        $task = (new Task('import/upload', 'upload-test'));

        $this->docSDK->tasks()->create($task);

        $response = $this->docSDK->tasks()->upload($task, fopen(__DIR__ . '/files/input.pdf', 'r'));

        $this->assertEquals(201, $response->getStatusCode());

        $this->docSDK->tasks()->wait($task);
        $this->assertEquals(Task::STATUS_FINISHED, $task->getStatus());

        $this->assertEquals('input.pdf', $task->getResult()->files[0]->filename);


    }


}
