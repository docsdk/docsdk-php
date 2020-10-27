<?php


namespace DocSDK\Resources;


use DocSDK\Models\ImportUploadTask;
use DocSDK\Models\ImportUrlTask;
use DocSDK\Models\Task;
use DocSDK\Models\TaskCollection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class TasksResource extends AbstractResource
{

    /**
     * @param string     $id
     * @param array|null $query
     *
     * @return Task
     */
    public function get(string $id, $query = null): Task
    {

        $response = $this->httpTransport->get($this->httpTransport->getBaseUri() . '/tasks/' . $id, $query ?? []);
        return $this->hydrator->createObjectByResponse(Task::class, $response);

    }


    /**
     * @param array|null $query
     *
     * @return TaskCollection
     */
    public function all($query = null): TaskCollection
    {
        $response = $this->httpTransport->get($this->httpTransport->getBaseUri() . '/tasks', $query ?? []);
        return $this->hydrator->hydrateArrayByResponse(new TaskCollection(), Task::class, $response);
    }


    /**
     * @param Task $task
     *
     * @return Task
     */
    public function create(Task $task): Task
    {
        $response = $this->httpTransport->post($this->httpTransport->getBaseUri() . '/' . $task->getOperation(),
            array_merge(
                ['name' => $task->getName()],
                $task->getPayload() ?? []
            )
        );
        return $this->hydrator->hydrateObjectByResponse($task, $response);
    }


    /**
     * @param Task       $task
     * @param array|null $query
     *
     * @return Task
     */
    public function refresh(Task $task, $query = null): Task
    {
        $response = $this->httpTransport->get($this->httpTransport->getBaseUri() . '/tasks/' . $task->getId(),
            $query ?? []);
        return $this->hydrator->hydrateObjectByResponse($task, $response);
    }

    /**
     * @param Task $task
     *
     * @return Task
     */
    public function wait(Task $task): Task
    {
        $response = $this->httpTransport->get($this->httpTransport->getBaseUri() . '/tasks/' . $task->getId() . '/wait');
        return $this->hydrator->hydrateObjectByResponse($task, $response);
    }

    /**
     * @param Task $task
     */
    public function delete(Task $task): void
    {
        $this->httpTransport->delete($this->httpTransport->getBaseUri() . '/tasks/' . $task->getId());
    }


    /**
     * @param Task                            $task
     * @param string|resource|StreamInterface $file
     *
     * @return ResponseInterface
     */
    public function upload(Task $task, $file): ResponseInterface
    {
        if ($task->getOperation() !== 'import/upload') {
            throw new \BadMethodCallException('The task operation is not import/upload');
        }
        if ($task->getStatus() !== Task::STATUS_WATING
            || !$task->getResult()
            || !isset($task->getResult()->form)) {
            throw new \BadMethodCallException('The task is not ready for uploading');
        }
        $form = $task->getResult()->form;
        return $this->httpTransport->upload($form->url, $file, (array)$form->parameters ?? []);
    }

}
