<?php

/**
 *
 * @author Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, Jan 27, 2017 - 11:24:42 AM
 */
class CollectablesPage
        extends DataObjectPage {

    private static $db = [
        'Collection' => "Enum('Collectable, CollectableCurrency, CollectableBanknote, CollectableCoin, CollectableStamp', 'Collectable')",
    ];

    /**
     */
    private static $group_code = 'collectors';
    private static $group_title = 'Collectors';
    private static $group_permission = 'CMS_ACCESS_CMSMain';

    public function canCreate($member = null, $context = []) {
        return true;
    }

    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeFieldFromTab("Root.Main", "Content");
        $fields->addFieldToTab('Root.Main', new DropdownField(
                'Collection', //
                'Collection', //
                singleton('CollectablesPage')->dbObject('Collection')->enumValues()
        ));

        return $fields;
    }

    protected function onBeforeWrite() {
        parent::onBeforeWrite();
        $this->getUserGroup();
    }

    /**
     * Returns/Creates the librarians group to assign CMS access.
     *
     * @return Group Librarians group
     */
    protected function getUserGroup() {
        $code = $this->config()->group_code;

        $group = Group::get()->filter('Code', $code)->first();

        if (!$group) {
            $group = new Group();
            $group->Title = $this->config()->group_title;
            $group->Code = $code;

            $group->write();

            $permission = new Permission();
            $permission->Code = $this->config()->group_permission;

            $group->Permissions()->add($permission);
        }

        return $group;
    }

}
