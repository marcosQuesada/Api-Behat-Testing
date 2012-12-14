<?php

namespace Acme\ApiBundle\Features\Context;

use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Behat\MinkExtension\Context\MinkContext;

use Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Guzzle\Http\Client;
use Guzzle\Http\Message\RequestInterface;
//
// Require 3rd-party libraries here:
//
   require_once 'PHPUnit/Autoload.php';
   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Feature context.
 */
class FeatureContext extends BehatContext //MinkContext if you want to test web
                  implements KernelAwareInterface
{
    private $kernel;
    private $parameters;
    protected $response;
    protected $rawData;
    protected $address;

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
     * @Given /^I set my address as "([^"]*)"$/
     */
    public function iSetMyAddressAs($arg1)
    {
        $this->address = $arg1;
    }


    /**
     * @Given /^I call "([^"]*)"$/
     */
    public function iCall($arg1)
    {
        $browser = new Client();
        $post = $browser->createRequest(RequestInterface::POST, $arg1);
        $post->setPostField('address', $this->address);

        $this->call = $post->send();


    }

    /**
     * @Then /^I get a response$/
     */
    public function iGetAResponse()
    {
        $this->response = $this->call->getBody(true);
        if (empty($this->response))
            throw new \Exception('Null Response from API call');
    }


    /**
     * @Given /^the response is JSON$/
     */
    public function theResponseIsJson()
    {
        $this->rawData = json_decode($this->response);

        if (empty($this->rawData))
            throw new \Exception('No Json Data found on Response from API call');
    }

    /**
     * @Given /^the response status code should be "([^"]*)"$/
     */
    public function theResponseStatusCodeShouldBe($arg1)
    {
        assertEquals($this->call->getStatusCode(), $arg1);
    }

    /**
     * @Given /^the post code is "([^"]*)"$/
     */
    public function theIs($arg1)
    {
        AssertEquals($this->rawData->postalCode, $arg1);
    }

//
// Place your definition and hook methods here:
//
//    /**
//     * @Given /^I have done something with "([^"]*)"$/
//     */
//    public function iHaveDoneSomethingWith($argument)
//    {
//        $container = $this->kernel->getContainer();
//        $container->get('some_service')->doSomethingWith($argument);
//    }
//
}
