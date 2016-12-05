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
 * @version 1.0, Dec 4, 2016 - 2:52:04 AM
 */
class CollectionPage
        extends AbstractCollectors {

    private static $db = array(
        'Collection' => "Enum('Collectable, CollectableCurrency, CollectableBanknote, CollectableCoin, CollectableStamp', 'Collectable')",
    );
    private static $defaults = array(
        'URLSegment' => 'collection',
        'Title' => 'Collection',
        'MenuTitle' => 'Collection',
    );
    private static $icon = "collectors/images/stamp.png";
    private static $url_segment = 'collection';
    private static $menu_title = 'collection';
    private static $allowed_children = 'none';
    private static $description = 'Adds a collection to your website.';

    public function canCreate($member = false) {
        return true;
    }

    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeFieldFromTab("Root.Main", "Content");
        $fields->addFieldToTab('Root.Main', new DropdownField(
                'Collection', //
                'Collection', //
                singleton('CollectionPage')->dbObject('Collection')->enumValues()
        ));

        return $fields;
    }

}

class CollectionPage_Controller
        extends AbstractCollectors_Controller {

    private static $url_handlers = array(
        '$ID' => 'index',
    );

    public function init() {
        parent::init();
    }

    public function index(SS_HTTPRequest $request) {
        return $this->collectable($request);
    }

    public function collectable(SS_HTTPRequest $request) {
        $id = $this->getRequest()->param('ID');

        if ($id) {
            $single = DataObject::get_by_id($this->Collection, (int) $id);
            $this->etalage(280, 410);

            $data = array(
                'Item' => $single,
                'Title' => $single->getTitle()
            );
        } else {
            $list = DataObject::get($this->Collection);

            $paginate = $this->getPaginated($list);

            $data = array(
                'Items' => $list,
                'Results' => $paginate
            );
        }

        if ($request->isAjax()) {
            return $this
                            ->customise($data)
                            ->renderWith('TheTree');
        }

        return $data;
    }

}