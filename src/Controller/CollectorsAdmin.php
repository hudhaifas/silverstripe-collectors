<?php

/**
 *
 * @author Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, Dec 3, 2016 - 10:57:18 PM
 */
class CollectorsAdmin
        extends ModelAdmin {

    private static $managed_models = array(
        'CollectableBanknote',
        'CollectableCoin',
        'CollectableStamp',
    );
    private static $url_segment = 'collectors';
    private static $menu_title = "Collectors";
    private static $menu_icon = "collectors/images/stamp.png";
    public $showImportForm = false;
    private static $tree_class = 'Collectors';

    public function getEditForm($id = null, $fields = null) {
        $form = parent::getEditForm($id, $fields);

        $grid = $form->Fields()->dataFieldByName('Collectors');
        if ($grid) {
            $grid->getConfig()->removeComponentsByType('GridFieldDetailForm');
            $grid->getConfig()->addComponent(new GridFieldSubsiteDetailForm());
        }

        return $form;
    }

}
