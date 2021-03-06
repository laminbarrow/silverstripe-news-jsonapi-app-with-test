<?php
namespace mysite;

use mysite\BaseController;
use mysite\DataObjects\Article;
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
        'deleteArticle',
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
        return $this->serveJSON(["The News Articles JSON API Server is online :)"]);
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

            $articles = Article::get()->toNestedArray();
            $arrayResult = $this->filterResponseDate($articles);
            return $this->serveJSON($arrayResult);

        } elseif ($request->isPOST()) {
            //do this for POST
            //we will create a new article
            return $this->createArticle($request);
        }

        return $this->serveJSON("The articles REST route endpoint only responds to GET and POST request types", 400);

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

            //create the article
            $article = Article::create($postedVars);
            $article->write();

            //serve the fresh Article with status 200
            return $this->serveJSON($this->filterResponseDate($article->toMap()), 200);
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
            $article = Article::get()->byID($request->param("ID"));
            if ($article) {
                $arrayResult = $this->filterResponseDate($article->toMap());
                return $this->serveJSON($arrayResult);
            } else {
                return $this->serveJSON("Sorry, that article cannot be found", 404);
            }

        } elseif ($request->isPUT() || $request->isPOST()) {
            //update article
            return $this->updateArticle($request);
        } elseif ($request->isDELETE()) {
            return $this->deleteArticle($request);
        }

        return $this->serveJSON("Sorry, this REST endpoint only responds to GET, POST & PUT requests", 400);
    }

    /**
     * Update Article
     *
     * @param \SilverStripe\Control\HTTPRequest $request
     * @return HTTPResponse
     */
    public function updateArticle(HTTPRequest $request)
    {

        if ($request->isPUT() || $request->isPOST()) {
            if ($request->isPUT() && $request->getBody()) {
                //parse the request body as this is a PUT request
                parse_str($request->getBody(), $requestVars);
                //validate the request
                $this->validateRequest($requestVars);
            } elseif ($request->isPOST() && $request->postVars()) {
                $requestVars = $request->postVars();
                $this->validateRequest($requestVars);
            }
            //update the article
            $article = Article::get()->byID($request->param("ID"));
            if ($article) {
                //fill in the article based on the fillable keys we have defined
                foreach ($this->fillableFields as $field) {
                    $article->$field = $requestVars[$field];
                }
                $article->write();
            } else {
                return $this->serveJSON("Sorry, that article cannot be found", 404);
            }

            //serve the fresh Article with status 200
            return $this->serveJSON($this->filterResponseDate($article->toMap()), 200);
        }
        return $this->serveJSON("You must specify the body of your PUT or POST VARS for POST request request", 400);
    }

    /**
     * Delete Article
     *
     * @return void
     */
    public function deleteArticle(HTTPRequest $request)
    {
        if ($request->isDELETE()) {
            $articleID = $request->param("ID");
            $article = Article::get()->byID($articleID);
            if ($article) {
                $article->delete();
                return $this->serveJSON("$articleID deleted", 200);
            }
            return $this->serveJSON("That article cannot be found article", 404);

        }
        return $this->serveJSON("You must define a DELETE request to delete this article", 400);
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
