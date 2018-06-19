<?php
namespace mysite\ModelAdmin;

use mysite\DataObjects\NewsItem;
use SilverStripe\Admin\ModelAdmin;

class NewsItemModelAdmin extends ModelAdmin
{
    /**
     * managed models
     *
     * @var array
     */
    private static $managed_models = [
        NewsItem::class,
    ];

    private static $url_segment = 'newsitems';

    private static $menu_title = 'News Items';
}
