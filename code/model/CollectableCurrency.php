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
        extends CollectableDenomination {

    private static $has_one = array(
        'BackImage' => 'Image',
    );
    private static $summary_fields = array(
        'Image.StripThumbnail',
        'BackImage.StripThumbnail',
        'Title',
        'Summary',
        'Description',
        'Denomination',
        'Currency',
        'TheDate',
        'Country',
        'Quantity',
    );

    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);

        $labels['BackImage'] = _t('Collectors.BACK_IMAGE', 'Back Image');
        $labels['BackImage.StripThumbnail'] = _t('Collectors.BACK_IMAGE', 'Back Image');

        return $labels;
    }

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        if ($field = $fields->fieldByName('Root.Main.Image')) {
            $field->setFolderName("collectors/currency");
        }

        if ($field = $fields->fieldByName('Root.Main.BackImage')) {
            $field->getValidator()->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif'));
            $field->setFolderName("collectors/currency");
        }

        $this->reorderField($fields, 'Image', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'BackImage', 'Root.Main', 'Root.Main');

        $this->reorderField($fields, 'Title', 'Root.Main', 'Root.Details');
        $this->reorderField($fields, 'Summary', 'Root.Main', 'Root.Details');
        $this->reorderField($fields, 'Description', 'Root.Main', 'Root.Details');

        $this->reorderField($fields, 'Denomination', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Currency', 'Root.Main', 'Root.Main');

        $this->reorderField($fields, 'Collector', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Country', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Year', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Calendar', 'Root.Main', 'Root.Main');

        $this->reorderField($fields, 'SerialNumber', 'Root.Main', 'Root.Details');

        return $fields;
    }

}