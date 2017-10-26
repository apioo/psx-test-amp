<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2017 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Server;

use Aerys\Request;
use Amp;
use Amp\ByteStream\InputStream;
use Amp\Promise;
use PSX\Framework\Config\Config;
use PSX\Framework\Dispatch\ControllerFactoryInterface;
use PSX\Framework\Dispatch\Dispatch;
use PSX\Framework\Exception\ConverterInterface;
use PSX\Framework\Loader\LoaderInterface;
use PSX\Http\Stream\StringStream;
use PSX\Uri\Uri;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Middleware
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Middleware
{
    /**
     * @var \PSX\Framework\Config\Config
     */
    protected $config;

    /**
     * @var \PSX\Framework\Loader\LoaderInterface
     */
    protected $loader;

    /**
     * @var \PSX\Framework\Dispatch\ControllerFactoryInterface
     */
    protected $factory;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var \PSX\Framework\Exception\ConverterInterface
     */
    protected $exceptionConverter;

    public function __construct(Config $config, LoaderInterface $loader, ControllerFactoryInterface $factory, EventDispatcherInterface $eventDispatcher, ConverterInterface $exceptionConverter)
    {
        $this->config             = $config;
        $this->loader             = $loader;
        $this->factory            = $factory;
        $this->eventDispatcher    = $eventDispatcher;
        $this->exceptionConverter = $exceptionConverter;
    }

    public function __invoke(Request $request, \Aerys\Response $response)
    {
        $dispatch = new Dispatch(
            $this->config,
            $this->loader,
            $this->factory,
            new Sender($response),
            $this->eventDispatcher,
            $this->exceptionConverter
        );

        $psxRequest  = new \PSX\Http\Request(new Uri($request->getUri()), $request->getMethod(), $request->getAllHeaders());
        $psxResponse = new \PSX\Http\Response();

        // read body
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            $body = yield $request->getBody();
            $psxRequest->setBody(new StringStream($body));
        }

        $dispatch->route($psxRequest, $psxResponse);
    }
}
