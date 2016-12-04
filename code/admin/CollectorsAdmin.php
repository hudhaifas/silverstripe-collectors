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
 * @version 1.0, Dec 3, 2016 - 10:57:18 PM
 */
class CollectorsAdmin
        extends ModelAdmin {

    private static $managed_models = array(
        'CurrencySet',
        'CollectableBanknote',
        'CollectableCoin',
        'StampSet',
        'CollectableStamp',
        'CollectableCollection',
    );
    private static $url_segment = 'collectors';
    private static $menu_title = "Collectors";
    private static $menu_icon = "collectors/images/stamp.png";
    public $showImportForm = false;
    private static $tree_class = 'Collectors';

    public function getEditForm($id = null, $fields = null) {
        $form = parent::getEditForm($id, $fields);

        $grid = $form->Fields()->dataFieldByName('Collectors');
        if ($grid) {
            $grid->getConfig()->removeComponentsByType('GridFieldDetailForm');
            $grid->getConfig()->addComponent(new GridFieldSubsiteDetailForm());
        }

        return $form;
    }

}
