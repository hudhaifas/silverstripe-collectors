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
 * @version 1.0, Dec 3, 2016 - 10:53:00 PM
 */
class CollectableCurrency
        extends Collectable {

    private static $db = array(
    );
    private static $translate = array(
    );
    private static $has_one = array(
        'BackImage' => 'Image',
        'Set' => 'CurrencySet',
    );
    private static $has_many = array(
    );
    private static $many_many = array(
    );
    private static $searchable_fields = array(
    );
    private static $summary_fields = array(
        'FrontImage.StripThumbnail',
        'BackImage.StripThumbnail',
        'Currency',
        'Denomination',
        'SerialNumber',
        'Country',
        'Year',
        'Quantity',
    );

    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);

        $labels['BackImage'] = _t('Collectors.BACK_IMAGE', 'Back Image');
        $labels['BackImage.StripThumbnail'] = _t('Collectors.BACK_IMAGE', 'Back Image');

        return $labels;
    }

    public function getCMSFields() {
        $self = & $this;

        $this->beforeUpdateCMSFields(function ($fields) use ($self) {
            if ($field = $fields->fieldByName('Root.Main.FrontImage')) {
                $field->setFolderName("collectors/currency");
            }

            if ($field = $fields->fieldByName('Root.Main.BackImage')) {
                $field->getValidator()->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif'));
                $field->setFolderName("collectors/currency");
            }

            $self->reorderField($fields, 'FrontImage', 'Root.Main', 'Root.Main');
            $self->reorderField($fields, 'BackImage', 'Root.Main', 'Root.Main');

            $self->reorderField($fields, 'Denomination', 'Root.Main', 'Root.Main');
            $self->reorderField($fields, 'Currency', 'Root.Main', 'Root.Main');

            $self->reorderField($fields, 'Country', 'Root.Main', 'Root.Main');
            $self->reorderField($fields, 'Year', 'Root.Main', 'Root.Main');
            $self->reorderField($fields, 'Date', 'Root.Main', 'Root.Main');
        });

        $fields = parent::getCMSFields();

        return $fields;
    }

    public function getTitle() {
        return $this->Country . ' (' . $this->Denomination . ' ' . $this->Currency . ')';
    }

}