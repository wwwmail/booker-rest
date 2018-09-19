<?php

namespace Application\Web;

class Request extends AbstractHttp {

    public function __construct(
    $uri = NULL, $method = NULL, array $headers = NULL, array $data = NULL, array $cookies = NULL)
    {
        if (!$headers)
            $this->headers = $_SERVER ?? array();
        else
            $this->headers = $headers;
        if (!$uri)
            $this->uri = $this->headers['PHP_SELF'] ?? '';
        else
            $this->uri = $uri;
        if (!$method)
            $this->method = $this->headers['REQUEST_METHOD'] ?? self::METHOD_GET;
        else
            $this->method = $method;
        if (!$data){
          
            $url = $_SERVER['REQUEST_URI'];
            $query_str = parse_url($url, PHP_URL_QUERY);
            parse_str($query_str, $query_params);
            
            if(is_array($query_params)){
                foreach($query_params as $key=>$value){
                    $this->setFilter($key);
                    $this->setFilterData($value);// = $value;
                }
            }

            $data = explode ('/', substr ($_SERVER['REQUEST_URI'], 1));
            if(is_array($data) && isset($data[1])){
                unset($data[0]);
                $this->data = $data[1];
                } else {
                    $this->data = null;
            }
        }else
            $this->data = $data;
        if (!$cookies)
            $this->cookies = $_COOKIE ?? array();
        else
            $this->cookies = $cookies;
        $this->setTransport();
    }

}
