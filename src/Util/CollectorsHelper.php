<?php

/**
 *
 * @author Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, Dec 4, 2016 - 2:00:45 AM
 */
class CollectorsHelper {

    private static $groups = array('collectors');
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
            $member = Member::currentUser();
        }

//        $groups = Config::inst()->get('GenealogistHelper', 'access_groups');
//        return $member && $member->inGroups($groups);
        return $member;
    }

    public static function search_all_collectables($request, $term) {
        $records = array();

//        // to fetch books that's name contains the given search term
//        $books = DataObject::get('Book')->filterAny(array(
//            'Name:PartialMatch' => $term,
//        ));
//
//        foreach ($books as $o) {
//            $records[] = $o;
//        }
//
//        // to fetch authors that's name contains the given search term
//        $authors = DataObject::get('BookAuthor')->filterAny(array(
//            'NickName:PartialMatch' => $term,
//            'FirstName:PartialMatch' => $term,
//            'LastName:PartialMatch' => $term,
//            'SurName:PartialMatch' => $term,
//        ));
//
//        foreach ($authors as $o) {
//            foreach ($o->Books() as $b) {
//                $records[] = $b;
//            }
//        }

        $result = new ArrayList($records);
        $result->removeDuplicates();
        return $result;
    }

}
