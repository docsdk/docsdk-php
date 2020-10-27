<?php


namespace DocSDK\Tests\Unit;

use DocSDK\DocSDK;
use GuzzleHttp\Psr7\Response;
use Http\Mock\Client;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{

    /**
     * @var DocSDK
     */
    protected $docSDK;

    /**
     * @var Client
     */
    protected $mockClient;


    public function setUp()
    {

        $this->docSDK = new DocSDK([
            'api_key'     => 'test_api_key',
            'http_client' => $this->getMockClient()
        ]);

        parent::setUp();
    }

    protected function getMockClient(): Client
    {

        if ($this->mockClient === null) {
            $this->mockClient = new Client();
            $this->mockClient->setDefaultResponse(new Response(404, [], ''));
        }

        return $this->mockClient;

    }

}
