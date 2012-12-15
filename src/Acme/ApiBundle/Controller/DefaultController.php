<?php

namespace Acme\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Method({"POST"})
     */
    public function indexAction(Request $request)
    {
        $params = $request->get('address', false);

        $response = $this->container->get('acme_api.gmaps')->getPostalCode($params);

        return new Response(json_encode(array('postalCode' => $response)));
    }

}