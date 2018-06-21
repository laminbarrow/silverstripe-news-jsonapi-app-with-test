<?php

use mysite\APIController;
use SilverStripe\Control\Director;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Core\Kernel;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\ORM\DB;

/**
 * API Test Controller
 */
class APIControllerTest extends FunctionalTest
{
    /**
     * Path to fixture data for this test run.
     * If passed as an array, multiple fixture files will be loaded.
     * Please note that you won't be able to refer with "=>" notation
     * between the fixtures, they act independent of each other.
     *
     * @var string|array
     */
    protected static $fixture_file = "APIControllerTest.yml";

    /**
     * Called once per test case ({@link SapphireTest} subclass).
     * This is different to {@link setUp()}, which gets called once
     * per method. Useful to initialize expensive operations which
     * don't change state for any called method inside the test,
     * e.g. dynamically adding an extension. See {@link teardownAfterClass()}
     * for tearing down the state again.
     *
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        //set to test mode
        $kernel = Injector::inst()->get(Kernel::class);
        $kernel->setEnvironment("test");

        //use an in memory SQLITE Database for the test
        if (Director::isTest()) {
            //use an SQLITE database in test mode
            DB::setConfig([
                'type' => 'SQLite3PDODatabase',
                "server" => 'none',
                "username" => 'none',
                "password" => 'none',
                'path' => ':memory:',
                'database' => 'test.sqlite',
            ]);
        }
    }

    ////////////////////////////
    //TESTS
    ///////////////////////////

    /**
     * Verify that the APIController class exists
     *
     */
    public function testAPIControllerClassExist()
    {
        $this->assertInstanceOf(
            APIController::class,
            APIController::create()
        );
    }

    /**
     * api/
     * API index route
     */
    public function testIndexRoute()
    {
        $apiRoute = $this->get('api/');

        //api route should load with status 200
        $this->assertEquals(200, $apiRoute->getStatusCode());

        //make sure that the response is json
        $this->assertJson($apiRoute->getBody());
    }

    /**
     * api/articles route
     *
     */
    public function testArticlesRoute()
    {
        $apiArticlesRoute = $this->get("api/articles");

        //make sure that the api/articles route
        //returns a 200 response code
        $this->assertEquals(200, $apiArticlesRoute->getStatusCode());

        //make sure that the response is json
        $this->assertJson($apiArticlesRoute->getBody());
    }

    public function testValidateThePresenceOfTheFirstArticle()
    {
        $apiArticleRoute = $this->get("api/articles/1");
        //make sure that the api/articles/1 route
        //returns a 200 response code
        $this->assertEquals(200, $apiArticleRoute->getStatusCode());

    }
}
