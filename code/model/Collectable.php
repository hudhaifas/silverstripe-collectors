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
        extends DataObject {

    private static $db = array(
        'SerialNumber' => 'Varchar(20)', // Unique serial number
        'Denomination' => 'Currency',
        'Currency' => 'Varchar(255)',
        'Country' => 'Varchar(255)',
        'Year' => 'Int',
        'Date' => 'Varchar(255)',
        'Quantity' => 'Int',
        'Description' => 'Varchar(255)',
        'Subject' => 'Varchar(255)',
    );
    private static $translate = array(
    );
    private static $has_one = array(
        'FrontImage' => 'Image',
    );
    private static $has_many = array(
    );
    private static $many_many = array(
        'Collections' => 'CollectableCollection',
    );
    private static $defaults = array(
        'Quantity' => 1,
    );
    private static $searchable_fields = array(
        'Denomination' => array(
            'field' => 'TextField',
            'filter' => 'PartialMatchFilter',
        ),
        'Currency' => array(
            'field' => 'TextField',
            'filter' => 'PartialMatchFilter',
        ),
        'Country' => array(
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
        'SerialNumber',
        'Denomination',
        'Currency',
        'Country',
        'Year',
        'TheDate',
        'Quantity',
        'Description',
        'Subject',
    );

    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);

        $labels['FrontImage'] = _t('Collectors.FRONT_IMAGE', 'Front Image');
        $labels['FrontImage.StripThumbnail'] = _t('Collectors.FRONT_IMAGE', 'Front Image');

        $labels['Denomination'] = _t('Collectors.DENOMINATION', 'Denomination');
        $labels['Currency'] = _t('Collectors.CURRENCY', 'Currency');

        $labels['Country'] = _t('Collectors.COUNTRY', 'Country');
        $labels['Year'] = _t('Collectors.YEAR', 'Year');
        $labels['Date'] = _t('Collectors.DATE', 'Date');
        $labels['TheDate'] = _t('Collectors.DATE', 'Date');

        $labels['SerialNumber'] = _t('Collectors.SERIAL_NUMBER', 'Serial Number');
        $labels['Quantity'] = _t('Collectors.QUANTITY', 'Quantity');
        $labels['Description'] = _t('Collectors.DESCRIPTION', 'Description');
        $labels['Subject'] = _t('Collectors.SUBJECT', 'Subject');
        $labels['Sets'] = _t('Collectors.SETS', 'Sets');
        $labels['Set'] = _t('Collectors.SET', 'Set');
        $labels['Collections'] = _t('Collectors.COLLECTIONS', 'Collections');

        return $labels;
    }

    public function getCMSFields() {
        $self = & $this;

        $this->beforeUpdateCMSFields(function ($fields) use ($self) {
            if ($field = $fields->fieldByName('Root.Main.FrontImage')) {
                $field->getValidator()->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif'));
                $field->setFolderName("collectors");
            }

            $self->reorderField($fields, 'FrontImage', 'Root.Main', 'Root.Main');

            $self->reorderField($fields, 'Denomination', 'Root.Main', 'Root.Main');
            $self->reorderField($fields, 'Currency', 'Root.Main', 'Root.Main');

            $self->reorderField($fields, 'Country', 'Root.Main', 'Root.Main');
            $self->reorderField($fields, 'Year', 'Root.Main', 'Root.Main');
            $self->reorderField($fields, 'Date', 'Root.Main', 'Root.Main');

            $self->reorderField($fields, 'SerialNumber', 'Root.Main', 'Root.Details');
            $self->reorderField($fields, 'Quantity', 'Root.Main', 'Root.Details');
            $self->reorderField($fields, 'Description', 'Root.Main', 'Root.Details');
            $self->reorderField($fields, 'Subject', 'Root.Main', 'Root.Details');
            $self->reorderField($fields, 'SetID', 'Root.Main', 'Root.Details');

            $fields->removeFieldFromTab('Root', 'Collections');
            $collectionField = TagField::create(
                            'Collections', //
                            _t('Collectors.COLLECTIONS', 'Collections'), // 
                            CollectableCollection::get(), //
                            $self->Collections()
            );
            $fields->addFieldToTab('Root.Details', $collectionField);
        });

        $fields = parent::getCMSFields();

        return $fields;
    }

    protected function onBeforeWrite() {
        parent::onBeforeWrite();
    }

    public function getTitle() {
        return $this->Country . ' (' . $this->Year . ')';
    }

    public function getDefaultSearchContext() {
        $fields = $this->scaffoldSearchFields(array(
            'restrictFields' => array(
                'Country',
                'Year',
            )
        ));

        $filters = array(
            'Country' => new PartialMatchFilter('Country'),
            'Year' => new PartialMatchFilter('Year'),
        );

        return new SearchContext(
                $this->class, $fields, $filters
        );
    }

    function Link($action = null) {
        return Director::get_current_page()->Link($this->ID);
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

    function TheDate() {
        return $this->Year ? $this->Year . ' ' . $this->Date : null;
    }

}