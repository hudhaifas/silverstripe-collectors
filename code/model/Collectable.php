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
 * @version 1.0, Dec 3, 2016 - 10:35:14 PM
 */
class Collectable
        extends DataObject
        implements SingleDataObject {

    private static $db = array(
        'SerialNumber' => 'Varchar(20)', // Unique serial number
        'Title' => 'Varchar(255)',
        'Summary' => 'Varchar(255)',
        'Description' => 'Text',
        'Collector' => 'Varchar(255)',
        'Year' => 'Int',
        'Calendar' => 'Varchar(255)',
        'Explanations' => 'Text',
    );
    private static $has_one = array(
        'FrontImage' => 'Image',
    );
    private static $many_many = array(
        'Collections' => 'CollectableCollection',
    );
    private static $searchable_fields = array(
        'Title' => array(
            'field' => 'TextField',
            'filter' => 'PartialMatchFilter',
        ),
        'Summary' => array(
            'field' => 'TextField',
            'filter' => 'PartialMatchFilter',
        ),
        'Description' => array(
            'field' => 'TextField',
            'filter' => 'PartialMatchFilter',
        ),
        'Collector' => array(
            'field' => 'TextField',
            'filter' => 'PartialMatchFilter',
        ),
        'Year' => array(
            'field' => 'NumericField',
            'filter' => 'PartialMatchFilter',
        ),
    );
    private static $summary_fields = array(
        'FrontImage.StripThumbnail',
        'Title',
        'Summary',
        'Description',
        'TheDate',
    );

    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);

        $labels['FrontImage'] = _t('Collectors.FRONT_IMAGE', 'Front Image');
        $labels['FrontImage.StripThumbnail'] = _t('Collectors.FRONT_IMAGE', 'Front Image');

        $labels['SerialNumber'] = _t('Collectors.SERIAL_NUMBER', 'Serial Number');
        $labels['Title'] = _t('Collectors.TITLE', 'Title');
        $labels['Description'] = _t('Collectors.DESCRIPTION', 'Description');
        $labels['Explanations'] = _t('Collectors.EXPLANATIONS', 'Explanations');
        $labels['Summary'] = _t('Collectors.SUMMARY', 'Summary');
        $labels['Collector'] = _t('Collectors.COLLECTOR', 'Collector');

        $labels['Year'] = _t('Collectors.YEAR', 'Year');
        $labels['Calendar'] = _t('Collectors.CALENDAR', 'Calendar');
        $labels['TheDate'] = _t('Collectors.DATE', 'Date');

        $labels['Collections'] = _t('Collectors.COLLECTIONS', 'Collections');

        return $labels;
    }

    public function geteeCMSFields() {
        $fields = parent::getCMSFields();

        if ($field = $fields->fieldByName('Root.Main.FrontImage')) {
            $field->getValidator()->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif'));
            $field->setFolderName("collectors");
        }

        $this->reorderField($fields, 'FrontImage', 'Root.Main', 'Root.Main');

        $this->reorderField($fields, 'Title', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Summary', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Description', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Collector', 'Root.Main', 'Root.Main');

        $this->reorderField($fields, 'Year', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Calendar', 'Root.Main', 'Root.Main');

        $detailsTab = new Tab('Details', _t('Collectors.DETAILS', 'Details'));
        $fields->insertAfter('Main', $detailsTab);
        $this->reorderField($fields, 'SerialNumber', 'Root.Main', 'Root.Details');
        $this->reorderField($fields, 'Explanations', 'Root.Main', 'Root.Details');

        $fields->removeFieldFromTab('Root', 'Collections');
        $collectionsField = TagField::create(
                        'Collections', //
                        _t('Collectors.COLLECTIONS', 'Collections'), // 
                        CollectableCollection::get(), //
                        $this->Collections()
        );
        $fields->addFieldToTab('Root.Details', $collectionsField);

        return $fields;
    }

    function Link($action = null) {
        return Director::get_current_page()->Link("show/$this->ID");
    }

    public function Subtitle() {
        $subtitle = '';
        if ($this->Title) {
            $subtitle = $this->Title;
        }


        if ($this->Date) {
            $subtitle .= ' (' . $this->Date . ')';
        } else if ($this->Year) {
            $subtitle .= ' (' . $this->Year . ')';
        }
        return $subtitle;
    }

    function TheDate() {
        return $this->Year ? $this->Year . ' ' . $this->Calendar : null;
    }

    /// Permissions ///

    function canCreate($member = false) {
        return CollectorsHelper::is_collector($member);
    }

    function canView($member = false) {
        return CollectorsHelper::is_collector($member);
    }

    function canDelete($member = false) {
        return CollectorsHelper::is_collector($member);
    }

    function canEdit($member = false) {
        return CollectorsHelper::is_collector($member);
    }

    /// Reorder ///

    function reorderField($fields, $name, $fromTab, $toTab, $disabled = false) {
        $field = $fields->fieldByName($fromTab . '.' . $name);

        if ($field) {
            $fields->removeFieldFromTab($fromTab, $name);
            $fields->addFieldToTab($toTab, $field);

            if ($disabled) {
                $field = $field->performDisabledTransformation();
            }
        }

        return $field;
    }

    function removeField($fields, $name, $fromTab) {
        $field = $fields->fieldByName($fromTab . '.' . $name);

        if ($field) {
            $fields->removeFieldFromTab($fromTab, $name);
        }

        return $field;
    }

    function trim($field) {
        if ($this->$field) {
            $this->$field = trim($this->$field);
        }
    }

    /// Single Data Object ///

    public function getObjectImage() {
        return $this->FrontImage();
    }

    public function getObjectLink() {
        return $this->Link();
    }

    public function getObjectRelated() {
        return $this->get()->sort('RAND()');
    }

    public function getObjectSummary() {
        $lists = array();

        if ($this->Subtitle()) {
            $lists[] = array(
                'Value' => $this->Subtitle()
            );
        }

        if ($this->Summary) {
            $lists[] = array(
                'Value' => $this->Summary
            );
        }

        return new ArrayList($lists);
    }

    public function getObjectTabs() {
        
    }

    public function isObjectDisabled() {
        return false;
    }

    public function getObjectTitle() {
        $title = '';

        if ($this->$this->Title) {
            $title = $this->Title;
        }

        if ($this->Date) {
            $title .= ' (' . $this->Date . ')';
        } else if ($this->Year) {
            $title .= ' (' . $this->Year . ')';
        }
        return $title;
    }

}