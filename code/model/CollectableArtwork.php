<?php

/*
 * MIT License
 *  
 * Copyright (c) 2017 Hudhaifa Shatnawi
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
 * @version 1.0, Jan 27, 2017 - 10:04:06 AM
 */
class CollectableArtwork
        extends Collectable {

    private static $db = array(
        'Date' => 'Date',
        'Texts' => 'HTMLText',
        'Artist' => 'Varchar(255)',
    );
    private static $default_sort = 'Date';

    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);

        $labels['Date'] = _t('Collectors.DATE', 'Date');
        $labels['Texts'] = _t('Collectors.TEXTS', 'Texts');
        $labels['Artist'] = _t('Collectors.ARTIST', 'Artist');

        return $labels;
    }

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        if ($field = $fields->fieldByName('Root.Main.FrontImage')) {
            $field->setFolderName("collectors/artworks");
        }

        if ($field = $fields->fieldByName('Root.Main.Date')) {
            $field->setConfig('showcalendar', true);
            $field->setConfig('dateformat', 'dd-MM-yyyy');
        }

        $this->reorderField($fields, 'FrontImage', 'Root.Main', 'Root.Main');

        $this->reorderField($fields, 'Title', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Artist', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Summary', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Description', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Texts', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Collections', 'Root.Main', 'Root.Main');

        $this->reorderField($fields, 'Date', 'Root.Main', 'Root.Details');
        $this->reorderField($fields, 'Year', 'Root.Main', 'Root.Details');
        $this->reorderField($fields, 'Calendar', 'Root.Main', 'Root.Details');
        $this->reorderField($fields, 'Country', 'Root.Main', 'Root.Details');
        $this->reorderField($fields, 'Collector', 'Root.Main', 'Root.Details');
        $this->reorderField($fields, 'SerialNumber', 'Root.Main', 'Root.Details');

        return $fields;
    }

    public function getObjectSummary() {
        $lists = array();

        if ($this->Subtitle()) {
            $lists[] = array(
                'Value' => $this->Subtitle()
            );
        }

        if ($this->Date) {
            $lists[] = array(
                'Title' => _t('Collectors.DATE', 'Date'),
                'Value' => $this->Date
            );
        }

        if ($this->Collector) {
            $lists[] = array(
                'Title' => _t('Collectors.COLLECTOR', 'Collector'),
                'Value' => $this->Collector
            );
        }

        if ($this->Description) {
            $lists[] = array(
                'Title' => _t('Collectors.DESCRIPTION', 'Description'),
                'BR' => 1,
                'Value' => $this->Description
            );
        }

        return new ArrayList($lists);
    }

    public function isObjectDisabled() {
        return false;
    }

    public function getObjectTabs() {
        $lists = array();
        if ($this->Texts) {
            $lists[] = array(
                'Title' => _t('Collectors.TEXTS', 'Texts'),
                'Content' => $this->Texts
            );
        }

        return new ArrayList($lists);
    }

}