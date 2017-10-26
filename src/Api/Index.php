<?php

namespace App\Api;

use PSX\Framework\Controller\ApiAbstract;
use PSX\Framework\Controller\Tool;

class Index extends ApiAbstract
{
    /**
     * @Inject
     * @var \PSX\Framework\Loader\ReverseRouter
     */
    protected $reverseRouter;

    public function onLoad()
    {
        parent::onLoad();

        $this->setBody([
            'message' => 'Welcome, this is a PSX sample application. It should help to bootstrap a project by providing all needed files and some examples.',
            'links'   => [
                [
                    'rel'   => 'routing',
                    'href'  => $this->reverseRouter->getUrl(Tool\RoutingController::class),
                    'title' => 'Gives an overview of all available routing definitions',
                ],
                [
                    'rel'   => 'documentation',
                    'href'  => $this->reverseRouter->getUrl(Tool\DocumentationController::class . '::doIndex'),
                    'title' => 'Generates an API documentation from all available endpoints',
                ],
            ]
        ]);
    }
}
