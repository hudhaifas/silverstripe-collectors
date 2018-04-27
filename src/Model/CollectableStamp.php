<?php

/**
 *
 * @author Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, Dec 3, 2016 - 5:38:49 PM
 */
class CollectableStamp
        extends CollectableDenomination {

    private static $db = [
        'Condition' => "Enum('USED, UNUSED', 'UNUSED')",
    ];
    private static $summary_fields = [
        'Image.StripThumbnail',
        'Title',
        'Summary',
        'Description',
        'TheDate',
        'Country',
        'Condition',
        'Quantity',
    ];

    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);

        $labels['Condition'] = _t('Collectors.CONDITION', 'Condition');

        return $labels;
    }

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        // Remove existing fields

        if ($field = $fields->fieldByName('Root.Main.Image')) {
            $field->getValidator()->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif']);
            $field->setFolderName("collectors/stamps");
        }

        $this->reorderField($fields, 'Image', 'Root.Main', 'Root.Main');

        $this->reorderField($fields, 'Title', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Summary', 'Root.Main', 'Root.Main');

        $this->reorderField($fields, 'Condition', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Country', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Year', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Calendar', 'Root.Main', 'Root.Main');

        $this->reorderField($fields, 'Quantity', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Denomination', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Currency', 'Root.Main', 'Root.Main');

        $this->reorderField($fields, 'SerialNumber', 'Root.Main', 'Root.Details');
        $this->reorderField($fields, 'Description', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Collector', 'Root.Main', 'Root.Details');

        return $fields;
    }

    public function TheCondition() {
        return _t('Collectors.' . $this->Condition, $this->Condition);
    }

    public function getObjectSummary() {
        $lists = parent::getObjectSummary();

        $item = [
            'Title' => _t('Collectors.CONDITION', 'Condition'),
            'Value' => $this->TheCondition()
        ];
        $lists->add($item);

        return $lists;
    }

    public function isCreatable() {
        return true;
    }

}
