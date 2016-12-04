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
        'Country' => 'Varchar(255)',
        'Year' => 'Int',
        'Quantity' => 'Int',
        'Description' => 'Varchar(255)',
        'Subject' => 'Varchar(255)',
    );
    private static $translate = array(
    );
    private static $has_one = array(
        'Image' => 'Image',
    );
    private static $has_many = array(
    );
    private static $many_many = array(
        'Collections' => 'CollectableCollection',
    );
    private static $searchable_fields = array(
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
        'Image.StripThumbnail',
        'SerialNumber',
        'Country',
        'Year',
        'Quantity',
        'Description',
        'Subject',
    );

    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);

        $labels['Image.StripThumbnail'] = _t('Collector.IMAGE', 'Image');
        $labels['SerialNumber'] = _t('Collector.SERIAL_NUMBER', 'Serial Number');
        $labels['Country'] = _t('Collector.COUNTRY', 'Country');
        $labels['Year'] = _t('Collector.YEAR', 'Year');
        $labels['Quantity'] = _t('Collector.QUANTITY', 'Quantity');
        $labels['Description'] = _t('Collector.DESCRIPTION', 'Description');
        $labels['Subject'] = _t('Collector.SUBJECT', 'Subject');
        $labels['Collections'] = _t('Collector.COLLECTIONS', 'Collections');

        return $labels;
    }

    public function getCMSFields() {
        $self = & $this;

        $this->beforeUpdateCMSFields(function ($fields) use ($self) {
            if ($field = $fields->fieldByName('Root.Main.Image')) {
                $field->getValidator()->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif'));
                $field->setFolderName("collectors");

                $fields->removeFieldFromTab('Root.Main', 'Image');
                $fields->addFieldToTab('Root.Main', $field);
            }

            $this->reorderField($fields, 'SerialNumber', 'Root.Main', 'Root.Main');
            $this->reorderField($fields, 'Country', 'Root.Main', 'Root.Main');
            $this->reorderField($fields, 'Year', 'Root.Main', 'Root.Main');

            $fields->removeFieldFromTab('Root', 'Collections');

            $collectionField = TagField::create(
                            'Collections', //
                            'Collections', // 
                            CollectableCollection::get(), //
                            $self->Collections()
            );
            $fields->addFieldToTab('Root.Details', $collectionField);

            $this->reorderField($fields, 'Quantity', 'Root.Main', 'Root.Details');
            $this->reorderField($fields, 'Description', 'Root.Main', 'Root.Details');
            $this->reorderField($fields, 'Subject', 'Root.Main', 'Root.Details');
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

}
