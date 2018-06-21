<?php

namespace mysite\DataObjects;

use Faker\Factory;
use SilverStripe\Control\Director;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\DB;

class Article extends DataObject
{
    /**
     * Override table name for this class. If ignored will default to FQN of class.
     * This option is not inheritable, and must be set on each class.
     * If left blank naming will default to the legacy (3.x) behaviour.
     *
     * @var string
     */
    private static $table_name = "Article";

    /**
     * db
     *
     * @var array
     */
    private static $db = [
        "Title" => "Varchar(255)",
        "Content" => "Text",
    ];

    /**
     * Add default records to database. This function is called whenever the
     * database is built, after the database tables have all been created. Overload
     * this to add default records when the database is built, but make sure you
     * call parent::requireDefaultRecords().
     *
     * @uses DataExtension->requireDefaultRecords()
     */
    public function requireDefaultRecords()
    {
        //let us create our selfs a few news items when running this in dev mode
        //when no Articles exists in dev or test mode
        if (Director::isDev() || Director::isTest()) {

            if (!self::get()->first()) {

                $newsItesmToCreate = 5;
                $faker = Factory::create();

                for ($i = 1; $i <= $newsItesmToCreate; $i++) {
                    $article = new self();
                    $article->Title = $faker->sentence;
                    $article->Content = $faker->text(200);
                    $article->write();
                    DB::alteration_message('New Article created', 'created');
                }
            }
        }
    }
}
