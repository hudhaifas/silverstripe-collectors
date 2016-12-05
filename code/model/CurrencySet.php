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
 * @version 1.0, Dec 4, 2016 - 12:08:57 AM
 */
class CurrencySet
        extends CollectableCurrency {

    private static $db = array(
        'Name' => "Varchar(255)",
    );
    private static $has_many = array(
        'Currencies' => 'CollectableCurrency'
    );
    private static $summary_fields = array(
        'Image.StripThumbnail',
        'Name',
        'Currency',
        'Country',
        'Year',
        'Quantity',
    );

    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);

        $labels['Name'] = _t('Collectors.NAME', 'Name');

        return $labels;
    }

    public function getCMSFields() {
        $self = & $this;

        $this->beforeUpdateCMSFields(function ($fields) use ($self) {
            $fields->removeFieldFromTab('Root.Main', 'BackImage');
            $fields->removeFieldFromTab('Root.Main', 'SerialNumber');
            $fields->removeFieldFromTab('Root.Value', 'SerialNumber');

            $self->reorderField($fields, 'Image', 'Root.Main', 'Root.Main');
            $self->reorderField($fields, 'Name', 'Root.Main', 'Root.Main');
            $self->reorderField($fields, 'Currency', 'Root.Main', 'Root.Main');
            $self->reorderField($fields, 'Country', 'Root.Main', 'Root.Main');
            $self->reorderField($fields, 'Year', 'Root.Main', 'Root.Main');
            $self->reorderField($fields, 'Quantity', 'Root.Main', 'Root.Main');
            
            $self->reorderField($fields, 'Description', 'Root.Main', 'Root.Details');
            $self->reorderField($fields, 'Subject', 'Root.Main', 'Root.Details');
        });

        $fields = parent::getCMSFields();

        return $fields;
    }

}
