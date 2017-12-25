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
 * @version 1.0, Jan 27, 2017 - 10:02:12 AM
 */
class CollectableDenomination
        extends Collectable {

    private static $db = array(
        'Denomination' => 'Currency',
        'Currency' => 'Varchar(255)',
        'Quantity' => 'Int',
        'Year' => 'Int',
        'Calendar' => 'Varchar(255)',
        'Country' => 'Varchar(255)',
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
        'Image.StripThumbnail',
        'Title',
        'Summary',
        'Description',
        'Denomination',
        'Currency',
        'TheDate',
        'Country',
        'Quantity',
    );
    private static $default_sort = 'Year';

    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);

        $labels['Denomination'] = _t('Collectors.DENOMINATION', 'Denomination');
        $labels['Currency'] = _t('Collectors.CURRENCY', 'Currency');
        $labels['Quantity'] = _t('Collectors.QUANTITY', 'Quantity');
        $labels['Country'] = _t('Collectors.COUNTRY', 'Country');
        $labels['Year'] = _t('Collectors.YEAR', 'Year');
        $labels['Calendar'] = _t('Collectors.CALENDAR', 'Calendar');
        $labels['TheDate'] = _t('Collectors.DATE', 'Date');

        return $labels;
    }

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        if ($field = $fields->fieldByName('Root.Main.Image')) {
            $field->getValidator()->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif'));
            $field->setFolderName("collectors");
        }

        $this->reorderField($fields, 'Image', 'Root.Main', 'Root.Main');

        $this->reorderField($fields, 'Title', 'Root.Main', 'Root.Details');
        $this->reorderField($fields, 'Summary', 'Root.Main', 'Root.Details');
        $this->reorderField($fields, 'Description', 'Root.Main', 'Root.Details');
        $this->reorderField($fields, 'Collector', 'Root.Main', 'Root.Main');

        $this->reorderField($fields, 'Country', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Year', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Calendar', 'Root.Main', 'Root.Main');

        $this->reorderField($fields, 'SerialNumber', 'Root.Main', 'Root.Details');

        return $fields;
    }

    public function getObjectTitle() {
        $title = '';

        if ($this->Title) {
            $title = $this->Title;
        }

        if ($this->Year) {
            $title .= ' (' . $this->TheDate() . ')';
        }

        return $title;
    }

    public function Subtitle() {
        $subtitle = '';
        if ($this->Title) {
            $subtitle = $this->Title;
        }

        if ($this->Year) {
            $subtitle .= ' (' . $this->TheDate() . ')';
        }

        return $subtitle;
    }

    function TheDate() {
        return $this->Year ? $this->Year . ' ' . $this->Calendar : null;
    }

    //////// SearchableDataObject //////// 
    public function getObjectRichSnippets() {
        $schema = array();

        $schema['@type'] = "CreativeWork";
        $schema['dateCreated'] = $this->TheDate();
        $schema['description'] = $this->Summary;

        return $schema;
    }

}
