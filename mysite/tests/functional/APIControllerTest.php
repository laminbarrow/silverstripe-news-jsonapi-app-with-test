<?php

use mysite\APIController;
use SilverStripe\Dev\FunctionalTest;

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
    protected static $fixture_file = null;

    /**
     * @var Boolean If set to TRUE, this will force a test database to be generated
     * in {@link setUp()}. Note that this flag is overruled by the presence of a
     * {@link $fixture_file}, which always forces a database build.
     *
     * @var bool
     */
    protected $usesDatabase = null;

    /**
     * Setup
     * This method is called once
     * per method in every test case
     *
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * tearDown
     * Called after each test method
     */
    protected function tearDown()
    {
        parent::tearDown();
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
}
