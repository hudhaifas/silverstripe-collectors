<?php

use SilverStripe\ORM\ArrayList;
use SilverStripe\Security\Security;

/**
 *
 * @author Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, Dec 4, 2016 - 2:00:45 AM
 */
class CollectorsHelper {

    private static $groups = ['collectors'];
    private static $libraryID = 1310;
    private static $patronID = 2;
    private static $bookID = 3;

    /**
     * Checks if the given member is allowed to access the Collectors module
     *
     * @param type $member
     * @return type
     */
    public static function is_collector($member = false) {
        // Get current member
        if (!$member) {
            $member = Security::getCurrentUser();
        }

        return $member;
    }

    public static function search_all_collectables($request, $term) {
        $records = [];

        $result = new ArrayList($records);
        $result->removeDuplicates();
        return $result;
    }

}
