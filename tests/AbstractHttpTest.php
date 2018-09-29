<?php

namespace tests;

use Application\Web\AbstractHttp;
use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;

class AbstractHttpTest extends TestCase {

    private $abstractHttp;

    protected function setUp()
    {
        $this->abstractHttp = new AbstractHttp();
    }

    protected function tearDown()
    {
        $this->abstractHttp = NULL;
    }

    public function testUriIsset()
    {
        $this->assertClassHasAttribute('uri', 'Application\Web\AbstractHttp');
    }

    public function testMethodIsset()
    {
        $this->assertClassHasAttribute('method', 'Application\Web\AbstractHttp');
    }

    public function testHeadersIsset()
    {
        $this->assertClassHasAttribute('headers', 'Application\Web\AbstractHttp');
    }

    public function testCookiesIsset()
    {
        $this->assertClassHasAttribute('cookies', 'Application\Web\AbstractHttp');
    }

    public function testMetaDataIsset()
    {
        $this->assertClassHasAttribute('metaData', 'Application\Web\AbstractHttp');
    }

    public function testTransportIsset()
    {
        $this->assertClassHasAttribute('transport', 'Application\Web\AbstractHttp');
    }

    public function testDataIsset()
    {
        $this->assertClassHasAttribute('data', 'Application\Web\AbstractHttp');
    }

    public function testFilterIsset()
    {
        $this->assertClassHasAttribute('filter', 'Application\Web\AbstractHttp');
    }

    public function testFilterDataIsset()
    {
        $this->assertClassHasAttribute('filterData', 'Application\Web\AbstractHttp');
    }

    public function testSetFilters()
    {
        $result = $this->abstractHttp->setFilter('room');
        $this->assertEquals('fetchByRoom', $this->abstractHttp->getFilter());
    }

    public function testSetFilterDataU()
    {
        $result = $this->abstractHttp->setFilterData('22');
        $this->assertEquals(22, $this->abstractHttp->getFilterData());
    }

    public function testSetFilter()
    {
        $result = $this->abstractHttp->setFilter('room');
        $this->assertEquals('fetchByRoom', $this->abstractHttp->getFilter());
    }

    public function testSetCookie()
    {
        $result = $this->abstractHttp->setCookies('room','2');
        $this->assertEquals('2', $this->abstractHttp->getCookiesByKey('room'));
    }

    public function testSetMetaData()
    {
        $result = $this->abstractHttp->setMetaData('data');
        $this->assertEquals('data', $this->abstractHttp->getMetaData());
    }

    public function testSetMethod()
    {
        $result = $this->abstractHttp->setMethod('POST');
        $this->assertEquals('POST', $this->abstractHttp->getMethod());
    }

    public function testGetDefaultMethod()
    {
        $this->assertEquals('GET', $this->abstractHttp->getMethod());
    }

    public function testHeaderByKey()
    {
        $result = $this->abstractHttp->setHeaderByKey('cache-control', 'public');
        $this->assertInternalType('array', $this->abstractHttp->getHeaders());
    }

    public function testSetHeaderByKey()
    {
        $result = $this->abstractHttp->setHeaderByKey('cache-control', 'public');
        $this->assertEquals('public', $this->abstractHttp->getHeaderByKey('cache-control'));
    }

    public function testSetUri()
    {
        $result = $this->abstractHttp->setUri('www.mail.cz');
        $this->assertEquals('www.mail.cz', $this->abstractHttp->getUri());
    }

    public function testSetUriWithParams()
    {
        $result = $this->abstractHttp->setUri('www.mail.cz', ['test' => 1, 'test2' => 2]);
        $this->assertEquals('www.mail.cz?test=1&test2=2', $this->abstractHttp->getUri());
    }

}
