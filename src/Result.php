<?php

namespace AndreasGlaser\KPC;

use AndreasGlaser\Helpers\ArrayHelper;
use AndreasGlaser\Helpers\StringHelper;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Result
 *
 * @package AndreasGlaser\KPC
 * @author  Andreas Glaser
 */
class Result
{

    /**
     * @var \GuzzleHttp\Psr7\Response
     */
    public $response;

    /**
     * @var string
     */
    public $contents;

    /**
     * @var \stdClass|null
     */
    public $decoded;

    /**
     * Result constructor.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @author Andreas Glaser
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
        $this->contents = $this->response->getBody()->getContents();
        $this->error = [];
        $this->success = false;

        // decode json
        if ($this->response->hasHeader('Content-Type')) {
            $contentType = ArrayHelper::getFirstValue($this->response->getHeader('Content-Type'));
            if (StringHelper::startsWith($contentType, 'application/json')) {
                $this->decoded = json_decode($this->contents, true);
                $this->error = $this->decoded['error'];
                $this->success = empty($this->error);
            }
        }
    }
}