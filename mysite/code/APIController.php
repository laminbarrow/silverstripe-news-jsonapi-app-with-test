<?php
namespace mysite;

use mysite\BaseController;
use mysite\DataObjects\NewsItem;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;

class APIController extends BaseController
{

    /**
     * Items to filter from json api response
     *
     * @var array
     */
    private $itemsToFilterFromResponse = [
        'ClassName',
        'RecordClassName',
    ];

    /**
     * Required Fields
     *
     * @var array
     */
    private $requiredFields = [
        "Title",
        "Content",
    ];

    /**
     * Fillable Fields
     *
     * @var array
     */
    private $fillableFields = [
        "Title",
        "Content",
    ];

    /**
     * @var array
     */
    private static $allowed_actions = [
        'index',
        'articles',
        'showArticle',
    ];

    /**
     * URL Handlers
     *
     */
    private static $url_handlers = [
        '$Action/$ID!' => 'showArticle',
    ];

    /**
     * index
     *
     */
    public function index()
    {
        return $this->serveJSON("The News JSON API Server is online :)");
    }

    /**
     * Get articles from the database and sends a json response
     * /api/articles
     *
     * @param \SilverStripe\Control\HTTPRequest $request
     * @return HTTPResponse
     */
    public function articles(HTTPRequest $request)
    {
        //do this for GET request
        if ($request->isGET()) {

            $articles = NewsItem::get()->toNestedArray();
            $arrayResult = $this->filterResponseDate($articles);
            return $this->serveJSON($arrayResult);

        } elseif ($request->isPOST()) {
            //do this for POST
            //we will create a new article
            return $this->createArticle($request);
        }

    }

    /**
     * Create Article
     *
     * @param \SilverStripe\Control\HTTPRequest $request
     * @return HTTPResponse
     */
    private function createArticle(HTTPRequest $request)
    {
        if ($postedVars = $request->postVars()) {
            //validate the request
            $this->validateRequest($postedVars);

            //create the news Item
            $newsItem = NewsItem::create($postedVars);
            $newsItem->write();

            //serve the fresh newsitem with status 200
            return $this->serveJSON($this->filterResponseDate($newsItem->toMap()), 200);
        }
        return $this->serveJSON("You must specify the body of your POST request", 400);
    }

    /**
     * Show Article
     *
     * @param \SilverStripe\Control\HTTPRequest $request
     * @return HTTPResponse
     */
    public function showArticle(HTTPRequest $request)
    {
        //do this for GET request
        if ($request->isGET()) {
            $article = NewsItem::get()->byID($request->param("ID"))->toMap();
            $arrayResult = $this->filterResponseDate($article);
            return $this->serveJSON($arrayResult);

        } elseif ($request->isPUT()) {
            //update article
            return $this->updateArticle($request);
        }

        return $this->serveJSON("Sorry, this REST endpoint only responds to GET requests", 400);
    }

    /**
     * Update Article
     *
     * @param \SilverStripe\Control\HTTPRequest $request
     * @return HTTPResponse
     */
    public function updateArticle(HTTPRequest $request)
    {

        if ($request->getBody()) {
            //parse the request body as this is a PUT request
            parse_str($request->getBody(), $requestVars);
            //validate the request
            $this->validateRequest($requestVars);

            //update the news Item
            $newsItem = NewsItem::get()->byID($request->param("ID"));
            //fill in the news item based on the fillable keys we have defined
            foreach ($this->fillableFields as $field) {
                $newsItem->$field = $requestVars[$field];
            }
            $newsItem->write();

            //serve the fresh newsitem with status 200
            return $this->serveJSON($this->filterResponseDate($newsItem->toMap()), 200);
        }
        return $this->serveJSON("You must specify the body of your POST request", 400);
    }

    /**
     * Validate Request
     *
     * @param array $items
     * @return void
     */
    private function validateRequest(array $items)
    {
        $validationMessages = [];
        $requiredFields = $this->requiredFields;
        $fillableFields = array_diff_key($items, array_fill_keys($this->fillableFields, ""));

        //required fields
        foreach ($requiredFields as $requiredField) {
            //validate the presence of required fields
            if (!array_key_exists($requiredField, $items)) {
                $validationMessages[] = "$requiredField is required";
            }
        }

        //check fillable fields
        if (count($fillableFields)) {
            foreach ($fillableFields as $key => $value) {
                $validationMessages[] = "$key is not in the fillable fields array";
            }
        }

        //return the validation error messages with a 400 status code and exit
        if (count($validationMessages)) {
            $response = $this->serveJSON($validationMessages, 400);
            echo $response->output();
            exit();
        }
        return true; //all requirements are set
    }

    /**
     * Filter response data
     *
     * @param array $data
     * @return array
     */
    private function filterResponseDate(array $items)
    {
        $arrayResult = [];
        $items = [$items]; //check items into a multi dimensional array
        $filterTheseFromResponse = array_fill_keys($this->itemsToFilterFromResponse, ""); //fill the keys with empty values

        //clear out the keys we dont need if items has many array items or just 1
        if (count($items) > 1) {
            foreach ($items as $item) {
                $arrayResult[] = array_diff_key($item, $filterTheseFromResponse);
            }
        } else {
            //pluck out the first array item
            $arrayResult = array_diff_key($items[0], $filterTheseFromResponse);
        }
        return $arrayResult;
    }
}
