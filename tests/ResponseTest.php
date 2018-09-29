<?php
namespace tests;

use Application\Web\Response;

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;



class ResponseTest extends TestCase
{
    private $response;
 
    protected function setUp()
    {
        $this->response = new Response();
    }

    protected function tearDown()
    {
        $this->response = NULL;
    }

    public function testSetStatus()
    {
        $result = $this->response->setStatus(200);
        $this->assertEquals(200, $this->response->getStatus());
    }

}
