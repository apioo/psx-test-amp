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

use Amp\ByteStream\Message;
use PSX\Http\Stream\StringStream;

/**
 * BodyStream
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class BodyStream extends StringStream
{
    protected $body;
    protected $isFilled = false;

    public function __construct(Message $body)
    {
        parent::__construct();

        $this->body = $body;
    }

    public function detach()
    {
        $this->fill();

        return parent::detach();
    }

    public function isWritable()
    {
        return false;
    }

    public function getContents($maxLength = -1)
    {
        $this->fill();

        return parent::getContents($maxLength);
    }

    public function read($maxLength)
    {
        $this->fill();

        return parent::read($maxLength);
    }

    public function __toString()
    {
        $this->fill();

        return parent::__toString();
    }

    protected function fill()
    {
        if (!$this->isFilled) {
            $buffer = '';
            foreach ($this->body->read() as $chunk) {
                $buffer.= $chunk;
            }

            $this->data     = $buffer;
            $this->isFilled = true;
        }
    }
}
