<?php

namespace Application\Web;

class AbstractHttp {

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const CONTENT_TYPE_HTML = 'text/html';
    const CONTENT_TYPE_JSON = 'application/json';
    const CONTENT_TYPE_FORM_URL_ENCODED = 'application/x-www-form-urlencoded';
    const HEADER_CONTENT_TYPE = 'Content-Type';
    const TRANSPORT_HTTP = 'http';
    const TRANSPORT_HTTPS = 'https';
    const STATUS_200 = '200';
    const STATUS_401 = '401';
    const STATUS_405 = '405';
    const STATUS_500 = '500';
    

    protected $uri; // i.e. http://xxx.com/yyy
    protected $method; // i.e. GET, PUT, POST, DELETE
    protected $headers; // HTTP headers
    protected $cookies; // cookies
    protected $metaData; // information about the transmission
    protected $transport; // i.e. http or https
    protected $data = array();
    protected $filter;
    protected $filterData;

    /**
     * Set filter
     * @param string $filter
     */
    public function setFilter($filter)
    {
        $this->filter = 'fetchBy' . ucfirst($filter);
    }

    /**
     * Get filter
     * return string
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Set filterData
     * @param int $filterData
     */
    public function setFilterData($filterData)
    {
        $this->filterData = (int) $filterData;
    }

    /**
     * Get filter data
     * return int
     */
    public function getFilterData()
    {
        return (int) $this->filterData;
    }

    /**
     * Set cookie
     * @param string $key
     * @param string $val
     */
    public function setCookies($key, $val)
    {
        $this->cookies[$key] = $val;
    }

    /**
     * Get cookie by key
     * @param string $key
     * @return string 
     */
    public function getCookiesByKey($key)
    {
        return $this->cookies[$key];
    }

    /**
     * Get cookies
     * @return null|array 
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * Set meta data
     * @param string $metadata
     */
    public function setMetaData($metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * Get meta data
     * @return string
     */
    public function getMetaData()
    {
        return $this->metadata;
    }

    /**
     * Set method
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * Get method
     * @return string
     */
    public function getMethod()
    {
        return $this->method ?? self::METHOD_GET;
    }

    /**
     * Set headers
     * @param string $key
     * @param string $val
     */
    public function setHeaderByKey($key, $value)
    {
        $this->headers[$key] = $value;
    }

    /**
     * Get headers
     * @return null|array 
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get headers by key
     * @return null|array 
     */
    public function getHeaderByKey($key)
    {
        return $this->headers[$key] ?? NULL;
    }

    /**
     * Set data
     * @param type $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Get data
     * @return type data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get data by key
     * @return type data | null
     */
    public function getDataByKey($key)
    {
        return $this->data[$key] ?? NULL;
    }

    /**
     * Get meta data by key
     * @return type data | null
     */
    public function getMetaDataByKey($key)
    {
        return $this->metaData[$key] ?? NULL;
    }

    /**
     * Set uri
     * @param string $uri
     * @param array $params
     */
    public function setUri($uri, array $params = NULL)
    {
        $this->uri = $uri;
        if ($params) {
            $this->uri .= '?' . http_build_query($params);
        }
    }

    /**
     * Get uri
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Get encoded data
     * @return string
     */
    public function getDataEncoded()
    {
        return http_build_query($this->getData());
    }

    /**
     * Set transport protocol (http , https)
     * @param string $transport
     */
    public function setTransport($transport = NULL)
    {
        if ($transport) {
            $this->transport = $transport;
        } else {
            if (substr($this->uri, 0, 5) == self::TRANSPORT_HTTPS) {
                $this->transport = self::TRANSPORT_HTTPS;
            } else {
                $this->transport = self::TRANSPORT_HTTP;
            }
        }
    }

}
