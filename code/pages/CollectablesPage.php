<?php

/*
 * MIT License
 *  
 * Copyright (c) 2016 Hudhaifa Shatnawi
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

/**
 *
 * @author Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, Jan 27, 2017 - 11:24:42 AM
 */
class CollectablesPage
        extends DataObjectPage {

    private static $db = array(
        'Collection' => "Enum('Collectable, CollectableCurrency, CollectableBanknote, CollectableCoin, CollectableStamp, CollectableArtwork, CollectableArticle, CollectableDocument, CollectablePhoto', 'Collectable')",
    );

    /**
     */
    private static $group_code = 'collectors';
    private static $group_title = 'Collectors';
    private static $group_permission = 'CMS_ACCESS_CMSMain';

    public function canCreate($member = false) {
        return true;
    }

    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeFieldFromTab("Root.Main", "Content");
        $fields->addFieldToTab('Root.Main', new DropdownField(
                'Collection', //
                'Collection', //
                singleton('CollectablesPage')->dbObject('Collection')->enumValues()
        ));

        return $fields;
    }

    protected function onBeforeWrite() {
        parent::onBeforeWrite();
        $this->getUserGroup();
    }

    /**
     * Returns/Creates the librarians group to assign CMS access.
     *
     * @return Group Librarians group
     */
    protected function getUserGroup() {
        $code = $this->config()->group_code;

        $group = Group::get()->filter('Code', $code)->first();

        if (!$group) {
            $group = new Group();
            $group->Title = $this->config()->group_title;
            $group->Code = $code;

            $group->write();

            $permission = new Permission();
            $permission->Code = $this->config()->group_permission;

            $group->Permissions()->add($permission);
        }

        return $group;
    }

}

class CollectablesPage_Controller
        extends DataObjectPage_Controller {

    protected function getObjectsList() {
        return DataObject::get($this->Collection)
                        ->filterByCallback(function($record) {
                            return $record->canView();
                        });
    }

    protected function searchObjects($list, $keywords) {
        return $list->filterAny(array(
                    'Title:PartialMatch' => $keywords,
                    'Summary:PartialMatch' => $keywords,
                    'Description:PartialMatch' => $keywords,
                    'Country:PartialMatch' => $keywords,
                    'Year:PartialMatch' => $keywords,
                    'SerialNumber:PartialMatch' => $keywords,
                    'Collector:PartialMatch' => $keywords,
        ));
    }

    protected function getFiltersList() {
//        $lists = array(
//            array(
//                'Title' => 'Categories',
//                'Items' => $this->getObjectsList()->Limit(6)
//            )
//        );
//
//        return new ArrayList($lists);
//        
        return null;
    }

}
