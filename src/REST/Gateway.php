<?php

namespace WakeOnWeb\SalesforceClient\REST;

class Gateway
{
    private $endpoint;
    private $version;

    public function __construct(string $endpoint, string $version)
    {
        $this->endpoint = $endpoint;
        $this->version = $version;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getServiceDataUrl(string $path = null): string
    {
        return $this->endpoint.'/services/data/v'.$this->version.'/'.$path;
    }
}
