<?php

namespace mysite;

use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\Convert;

class BaseController extends Controller
{

    /**
     * Serve JSON
     *
     * @param string $body
     * @param integer $statusCode
     * @param string $statusDescription
     * @return HTTPResponse
     */
    public function serveJSON($body, int $statusCode = 200, $statusDescription = null)
    {
        $response = new HTTPResponse(
            Convert::raw2json(
                $body
            ),
            $statusCode,
            $statusDescription = null
        );
        $response->addHeader("Content-Type", "application/json; charset=utf-8");
        return $response;
    }

}
