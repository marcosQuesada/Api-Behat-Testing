<?php

namespace Acme\DemoBundle\Features\Context;

use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Behat\MinkExtension\Context\MinkContext;

use Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use Behat\Mink\Mink,
    Behat\Mink\Session,
    Behat\Mink\Driver\GoutteDriver,
    Behat\Mink\Driver\Goutte\Client as GoutteClient;
use Guzzle\Http\Client;

//
// Require 3rd-party libraries here:
//
require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Feature context.
 */
class FeatureContext extends MinkContext //MinkContext if you want to test web
                  implements KernelAwareInterface
{
    private $kernel;
    private $parameters;
    protected $baseUrl = 'http://maps.googleapis.com/maps/api/geocode/json?address=';
    protected $apUrl;
    protected $response;
    protected $call;
    protected $content;
    protected $addressComponents;


    /**
     * Initializes context with parameters from behat.yml.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Sets HttpKernel instance.
     * This method will be automatically called by Symfony2Extension ContextInitializer.
     *
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given /^My Home Address is "([^"]*)"$/
     */
    public function myHomeAddressIs($arg1)
    {
        $parameters = $this->processArguments($arg1);
        $this->apiUrl = $this->getGoogleMapsUrl($parameters);
    }


    /**
     * @Given /^I call GeoCode API$/
     */
    public function iCallGeocodeApi()
    {
        $browser = new Client();
        $this->call = $browser->get($this->apiUrl)->send();

        $this->response = $this->call->getBody(true);
    }

    /**
     * @Then /^I get a response$/
     */
    public function iGetAResponse()
    {
        if (empty($this->response)) {
            throw new \Exception('Null Response from API call');
        }
    }

    /**
     * @Given /^the response is JSON$/
     */
    public function theResponseIsJson()
    {
        $this->content = json_decode($this->response);

        if (empty( $this->content)) {
            throw new \Exception("Response was not JSON\n" . $this->response);
        }
    }

    /**
     * @Given /^the response code is (\d+)$/
     */
    public function theResponseCodeIs($arg1)
    {
        assertEquals($this->call->getStatusCode(), 200);
    }

    /**
     * @Given /^the response has "([^"]*)" key$/
     */
    public function theResponseHasKey($arg1)
    {
        assertTrue(isset($this->content->$arg1));
    }

    /**
     * @Given /^the "([^"]*)" has "([^"]*)"$/
     */
    public function theHas($arg1, $arg2)
    {
        $results = $this->content->$arg1;

        assertTrue(isset($results[0]->$arg2));

        $this->addressComponents = $results[0]->$arg2;

    }

    /**
     * @Given /^in "([^"]*)" has "([^"]*)"$/
     */
    public function inHas($arg1, $arg2)
    {
        $mainKey = $this->processArguments($arg1, 'camelCase');
        $key = $this->processArguments($arg2,  'underscore');

        if (!is_array($this->$mainKey))
            throw new \Exception('Results is not an array');

        if (!array_key_exists(6, $this->$mainKey))
            throw new \Exception('Key number 6 not exists in results array');

        $postalCode = $this->addressComponents[6];

        if (!isset($postalCode->long_name))
            throw new \Exception('Not long_name attribute found in postalCode Obj');

        if ($postalCode->types[0] != $key)
            throw new \Exception('Position 0 not found in types attribute');

        $this->postalCode = $postalCode->long_name;
    }

    /**
     * @Given /^Postal Code is "([^"]*)"$/
     */
    public function postalCodeIs($arg1)
    {
        assertEquals($arg1, $this->postalCode);
    }

    protected function getGoogleMapsUrl($queryString)
    {
       return $this->baseUrl . $queryString . '&sensor=false';
    }

    protected function processArguments($arguments, $settedKey = 'parse')
    {
        $parts = explode(' ', $arguments);

        if ($settedKey === 'camelCase')
            return strtolower($parts[0]).ucfirst(strtolower($parts[1]));

        if ($settedKey === 'underscore')
            return strtolower(implode('_', $parts));

        return implode('+', $parts);
    }

}
