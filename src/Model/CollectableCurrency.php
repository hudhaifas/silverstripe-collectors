<?php

use SilverStripe\Assets\Image;
use SilverStripe\Core\Manifest\ModuleLoader;

/**
 *
 * @author Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, Dec 3, 2016 - 10:53:00 PM
 */
class CollectableCurrency
        extends CollectableDenomination {

    private static $has_one = [
        'BackImage' => Image::class,
    ];
    private static $summary_fields = [
        'Image',
        'BackImage',
        'Title',
        'Summary',
        'Description',
        'Denomination',
        'Currency',
        'TheDate',
        'Country',
        'Quantity',
    ];

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
            $field->getValidator()->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif']);
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

    public function getObjectDefaultImage() {
        return ModuleLoader::getModule('hudhaifas/silverstripe-collectors')->getResource('res/images/default-coin.png')->getURL();
    }

    public function getObjectTabs() {
        $lists = parent::getObjectTabs();

        if ($this->BackImage()->exists()) {
            $item = [
                'Title' => _t('Collectors.OTHER_SIDE', 'Other Side'),
                'Content' => $this
                        ->renderWith('Includes/Tab_BackImages')
            ];
            $lists->add($item);
        }

        return $lists;
    }

}
