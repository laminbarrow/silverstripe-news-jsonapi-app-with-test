<?php

use mysite\APIController;
use SilverStripe\Dev\SapphireTest;

/**
 * API Test Controller
 */
class APIControllerTest extends SapphireTest
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
     * @return void
     */
    public function testAPIControllerClassExist()
    {
        $this->assertInstanceOf(APIController::class, APIController::create());
    }
}
