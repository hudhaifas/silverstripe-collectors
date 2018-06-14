<?php

use SilverStripe\Admin\ModelAdmin;

/**
 *
 * @author Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, Dec 3, 2016 - 10:57:18 PM
 */
class CollectorsAdmin
        extends ModelAdmin {

    private static $managed_models = [
        CollectableBanknote::class,
        CollectableCoin::class,
        CollectableStamp::class,
    ];
    private static $url_segment = 'collectors';
    private static $menu_title = "Collectors";
    private static $menu_icon = "hudhaifas/silverstripe-collectors: res/images/stamp.png";

}
