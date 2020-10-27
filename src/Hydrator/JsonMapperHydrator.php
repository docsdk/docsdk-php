<?php


namespace DocSDK\Hydrator;


use DocSDK\Exceptions\UnexpectedDataException;
use DocSDK\Models\ExportUrlTask;
use DocSDK\Models\ImportUploadTask;
use DocSDK\Models\Task;
use JsonMapper;
use Psr\Http\Message\ResponseInterface;

class JsonMapperHydrator implements HydratorInterface
{

    /**
     * @var JsonMapper
     */
    protected $jsonMapper;

    /**
     * JsonMapperHydrator constructor.
     */
    public function __construct()
    {
        $this->jsonMapper = new JsonMapper();
        $this->jsonMapper->bIgnoreVisibility = true;
    }


    /**
     * @param $object
     * @param $data
     *
     * @return object
     * @throws UnexpectedDataException
     */
    public function hydrateObject($object, $data)
    {
        try {
            return $this->jsonMapper->map($data, $object);
        } catch (\JsonMapper_Exception $exception) {
            throw new UnexpectedDataException($exception);
        }
    }


    /**
     * @param $array
     * @param $objectClass
     * @param $data
     *
     * @return object
     */
    public function hydrateArray($array, string $objectClass, $data)
    {
        try {
            return $this->jsonMapper->map($data, $array, $objectClass);
        } catch (\JsonMapper_Exception $exception) {
            throw new UnexpectedDataException($exception);
        }
    }


    /**
     * @param                   $object
     * @param ResponseInterface $response
     *
     * @return object
     * @throws UnexpectedDataException
     */
    public function hydrateObjectByResponse($object, ResponseInterface $response)
    {
        $body = json_decode($response->getBody());
        try {
            return $this->jsonMapper->map($body->data, $object);
        } catch (\JsonMapper_Exception $exception) {
            throw new UnexpectedDataException($exception);
        }
    }


    /**
     * @param string            $class
     * @param ResponseInterface $response
     *
     * @return object
     * @throws UnexpectedDataException
     */
    public function createObjectByResponse(string $class, ResponseInterface $response)
    {
        $body = json_decode($response->getBody());
        return $this->hydrateObjectByResponse($this->jsonMapper->createInstance($class, false, $body->data), $response);
    }


    /**
     * @param                   $array
     * @param string            $objectClass
     * @param ResponseInterface $response
     *
     * @return mixed
     * @throws UnexpectedDataException
     */
    public function hydrateArrayByResponse($array, string $objectClass, ResponseInterface $response)
    {
        $body = json_decode($response->getBody());
        try {
            return $this->jsonMapper->mapArray($body->data, $array, $objectClass);
        } catch (\JsonMapper_Exception $exception) {
            throw new UnexpectedDataException($exception);
        }
    }

}
