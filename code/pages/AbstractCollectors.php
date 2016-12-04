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
 * @version 1.0, Dec 4, 2016 - 1:54:23 AM
 */
class AbstractCollectors
        extends Page {

    private static $db = array(
    );
    private static $has_one = array(
    );
    private static $has_many = array(
    );
    private static $defaults = array(
    );

    /**
     */
    private static $group_code = 'collectors';
    private static $group_title = 'Collectors';
    private static $group_permission = 'CMS_ACCESS_CMSMain';

    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeFieldFromTab("Root.Main", "Content");

        return $fields;
    }

    public function canCreate($member = false) {
        return false;
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

class AbstractCollectors_Controller
        extends Page_Controller {

    private static $allowed_actions = array(
        'stamps',
        'banknotes',
        'coins',
        // Search Actions
        'SearchCollectable',
        'doSearchCollectable'
    );
    private static $url_handlers = array(
        'stamps/$ID' => 'stamps',
        'banknotes/$ID' => 'banknotes',
        'coins/$ID' => 'coins',
    );

    public function init() {
        parent::init();

        Requirements::css("collectors/css/collectors.css");
        if ($this->isRTL()) {
            Requirements::css("collectors/css/collectors-rtl.css");
        }
    }

    public function getDBVersion() {
        return DB::get_conn()->getVersion();
    }

    /// Pagination ///
    public function getPaginated($list, $length = 9) {
        $paginate = new PaginatedList($list, $this->request);
        $paginate->setPageLength($length);

        return $paginate;
    }

    /// Search Book ///
    public function SearchCollectable() {
        $context = singleton('Collectable')->getDefaultSearchContext();
        $fields = $context->getSearchFields();
        $form = new Form($this, 'SearchCollectable', $fields, new FieldList(new FormAction('doSearchCollectable')));
        $form->setTemplate('Collectors_SearchCollectable');
//        $form->setFormMethod('GET');
//        $form->disableSecurityToken();
//        $form->setFormAction($this->Link());

        return $form;
    }

    public function doSearchCollectable($data, $form) {
        $term = $data['Form_SearchCollectable_SearchTerm'];

        $books = CollectorsHelper::search_all_collectables($this->request, $term);
        $title = _t('Collectors.SEARCH_RESULTS', 'Search Results') . ': ' . $data['Form_SearchCollectable_SearchTerm'];

        if ($books) {
            $paginate = $this->getPaginated($books);

            return $this
                            ->customise(array(
                                'Books' => $books,
                                'Results' => $paginate,
                                'Title' => $title
                            ))
                            ->renderWith(array('Library_Books', 'Page'));
        } else {
            return $this->httpError(404, 'No books could be found!');
        }
    }

    /// Sub Pages ///
    public function stamps() {
        $id = $this->getRequest()->param('ID');

        if ($id) {
            $stamps = CollectableStamp::get();
        } else {
            $stamps = CollectableStamp::get();
        }

        if ($stamps) {
            $paginate = $this->getPaginated($stamps);

            return $this
                            ->customise(array(
                                'Stamps' => $stamps,
                                'Results' => $paginate,
                                'Title' => _t('Collectors.STAMPS_LIST', 'Stamps List')
                            ))
                            ->renderWith(array('Collectors_Stamps', 'Page'));
        } else {
            return $this->httpError(404, 'No books could be found!');
        }
    }

    /// Get ///
    public function getCollectablesList($filters = array()) {
        $collectables = Collectable::get()->filter($filters);
        return $collectables;
    }

    public function getStampsList($filters = array()) {
        $stamps = CollectableStamp::get()->filter($filters);
        return $stamps;
    }

    public function getCoinsList($filters = array()) {
        $coins = CollectableCoin::get()->filter($filters);
        return $coins;
    }

    public function getBanknotesList($filters = array()) {
        $banknotes = CollectableBanknote::get()->filter($filters);
        return $banknotes;
    }

    public function getCurrenciesList($filters = array()) {
        $currencies = CollectableCurrency::get()->filter($filters);
        return $currencies;
    }

    public function getCollectionsList() {
        //TODO: fix this exception on MySQL > 5.7
        if ($this->getDBVersion() > '5.6') {
            $categories = CollectableCollection::get();
        } else {
            $categories = CollectableCollection::get()
                    ->setQueriedColumns(['ID', 'Title', 'Count(*)'])
                    ->leftJoin('Collectable_Collections', 'cc.CollectableCollectionID = CollectableCollection.ID', 'cc')
                    ->sort('Count(*) DESC')
                    ->alterDataQuery(function($query) {
                $query->groupBy('CollectableCollection.ID');
            });
        }

        return $categories;
    }

}
