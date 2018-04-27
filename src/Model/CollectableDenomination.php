<?php

use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\TextField;

/**
 *
 * @author Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, Jan 27, 2017 - 10:02:12 AM
 */
class CollectableDenomination
        extends Collectable {

    private static $db = [
        'Denomination' => 'Currency',
        'Currency' => 'Varchar(255)',
        'Quantity' => 'Int',
        'Year' => 'Int',
        'Calendar' => 'Varchar(255)',
        'Country' => 'Varchar(255)',
    ];
    private static $defaults = [
        'Quantity' => 1,
    ];
    private static $searchable_fields = [
        'Denomination' => [
            'field' => TextField::class,
            'filter' => 'PartialMatchFilter',
        ],
        'Currency' => [
            'field' => TextField::class,
            'filter' => 'PartialMatchFilter',
        ],
        'Country' => [
            'field' => TextField::class,
            'filter' => 'PartialMatchFilter',
        ],
        'Year' => [
            'field' => NumericField::class,
            'filter' => 'PartialMatchFilter',
        ],
    ];
    private static $summary_fields = [
        'Image.StripThumbnail',
        'Title',
        'Summary',
        'Description',
        'Denomination',
        'Currency',
        'TheDate',
        'Country',
        'Quantity',
    ];
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
            $field->getValidator()->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif']);
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

    public function getObjectSummary() {
        $lists = parent::getObjectSummary();

        if ($this->Country) {
            $item = [
                'Title' => _t('Collectors.COUNTRY', 'Country'),
                'Value' => $this->Country
            ];
            $lists->add($item);
        }

        if ($this->Description) {
            $item = [
                'Title' => _t('Collectors.DESCRIPTION', 'Description'),
                'Value' => $this->Description
            ];
            $lists->add($item);
        }

        if ($this->Denomination && $this->Currency) {
            $item = [
                'Title' => _t('Collectors.VALUE', 'Value'),
                'Value' => $this->Denomination . ' ' . $this->Currency
            ];
            $lists->add($item);
        }

        if ($this->TheDate()) {
            $item = [
                'Title' => _t('Collectors.DATE', 'Date'),
                'Value' => $this->TheDate()
            ];
            $lists->add($item);
        }

        if ($this->Quantity) {
            $item = [
                'Title' => _t('Collectors.QUANTITY', 'Quantity'),
                'Value' => $this->Quantity
            ];
            $lists->add($item);
        }

        return $lists;
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
        $schema = [];

        $schema['@type'] = "CreativeWork";
        $schema['dateCreated'] = $this->TheDate();
        $schema['description'] = $this->Summary;

        return $schema;
    }

}
