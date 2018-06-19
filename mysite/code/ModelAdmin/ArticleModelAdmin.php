<?php
namespace mysite\ModelAdmin;

use mysite\DataObjects\Article;
use SilverStripe\Admin\ModelAdmin;

class ArticleModelAdmin extends ModelAdmin
{
    /**
     * managed models
     *
     * @var array
     */
    private static $managed_models = [
        Article::class,
    ];

    private static $url_segment = 'articles';

    private static $menu_title = 'Articles';
}
