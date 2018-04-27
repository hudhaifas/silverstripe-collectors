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
        'CollectableBanknote',
        'CollectableCoin',
        'CollectableStamp',
    ];
    private static $url_segment = 'collectors';
    private static $menu_title = "Collectors";
    private static $menu_icon = "collectors/images/stamp.png";

}
