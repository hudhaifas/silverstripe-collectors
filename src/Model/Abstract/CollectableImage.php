<?php

/**
 *
 * @author Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, Apr 30, 2017 - 11:02:04 AM
 */
class CollectableImage
        extends DataObject {

    private static $db = array(
        'Title' => 'Varchar(255)',
        'Description' => 'Text',
    );
    private static $has_one = array(
        'Image' => 'Image',
        'Collectable' => 'Collectable',
    );

    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);

        $labels['Image'] = _t('Collectors.IMAGE', 'Image');
        $labels['Description'] = _t('Collectors.DESCRIPTION', 'Description');
        $labels['Collectable'] = _t('Collectors.COLLECTABLE', 'Collectable');

        return $labels;
    }

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        if ($field = $fields->fieldByName('Root.Main.Image')) {
            $field->getValidator()->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif'));
            $field->setFolderName("collectors");
        }
        return $fields;
    }

}
