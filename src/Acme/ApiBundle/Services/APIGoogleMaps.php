<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marcos
 * Date: 14/12/12
 * Time: 19:32
 * To change this template use File | Settings | File Templates.
 */
Namespace Acme\ApiBundle\Services;
use Guzzle\Http\Client;

class APIGoogleMaps
{
    protected $baseUrl;

    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function getPostalCode($address)
    {
        if (!$address)
            return;

        $browser = new Client();
        $content = $browser->get($this->getGoogleMapsUrl($address))->send()->getBody(true);

        return $this->getPostalCodeFromContent($content);
    }

    protected function getGoogleMapsUrl($queryString)
    {
        return $this->baseUrl . $queryString . '&sensor=false';
    }

    protected function getPostalCodeFromContent($content)
    {
        $decode =  json_decode($content);

        return $decode->results[0]->address_components[6]->long_name;
    }
}
