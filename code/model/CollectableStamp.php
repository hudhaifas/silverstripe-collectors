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
 * @version 1.0, Dec 3, 2016 - 5:38:49 PM
 */
class CollectableStamp
        extends Collectable {

    private static $db = array(
        'Condition' => "Enum('Used, Unused', 'Unused')",
    );
    private static $translate = array(
    );
    private static $has_one = array(
    );
    private static $has_many = array(
    );
    private static $many_many = array(
    );
    private static $searchable_fields = array(
        'Condition' => array(
            'field' => 'TextField',
            'filter' => 'PartialMatchFilter',
        )
    );
    private static $summary_fields = array(
        'Image.StripThumbnail',
        'SerialNumber',
        'Country',
        'Year',
        'Condition',
        'Quantity',
        'Description',
        'Subject',
    );

    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);

        $labels['Condition'] = _t('Collector.CONDITION', 'Condition');

        return $labels;
    }

    public function getCMSFields() {
        $self = & $this;

        $this->beforeUpdateCMSFields(function ($fields) use ($self) {
            if ($field = $fields->fieldByName('Root.Main.Image')) {
                $field->getValidator()->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif'));
                $field->setFolderName("collector");

                $fields->removeFieldFromTab('Root.Main', 'Image');
                $fields->addFieldToTab('Root.Main', $field);
            }

            $this->reorderField($fields, 'SerialNumber', 'Root.Main', 'Root.Main');
            $this->reorderField($fields, 'Country', 'Root.Main', 'Root.Main');
            $this->reorderField($fields, 'Year', 'Root.Main', 'Root.Main');
            $this->reorderField($fields, 'Quantity', 'Root.Main', 'Root.Main');
            $this->reorderField($fields, 'Condition', 'Root.Main', 'Root.Main');
            $this->reorderField($fields, 'Description', 'Root.Main', 'Root.Main');
            $this->reorderField($fields, 'Subject', 'Root.Main', 'Root.Main');
        });

        $fields = parent::getCMSFields();

        return $fields;
    }

}
