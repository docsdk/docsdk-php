<?php


namespace DocSDK\Tests\Integration;

use DocSDK\DocSDK;
use Http\Mock\Client;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{

    /**
     * @var DocSDK
     */
    protected $docSDK;


    public function setUp()
    {

        $this->docSDK = new DocSDK([
            'sandbox' => true,
            'api_key' => getenv('DOCSDK_API_KEY')
        ]);

        parent::setUp();
    }


}
