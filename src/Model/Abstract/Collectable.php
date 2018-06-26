<?php

use HudhaifaS\DOM\Model\ManageableDataObject;
use HudhaifaS\DOM\Model\SearchableDataObject;
use HudhaifaS\DOM\Model\SociableDataObject;
use SilverStripe\Assets\Image;
use SilverStripe\Control\Director;
use SilverStripe\Forms\ListboxField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Group;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Versioned\Versioned;

/**
 *
 * @author Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, Dec 3, 2016 - 10:35:14 PM
 */
class Collectable
        extends DataObject
        implements ManageableDataObject, SearchableDataObject, SociableDataObject {

    private static $table_name = 'Collectable';
    private static $db = [
        'SerialNumber' => 'Varchar(20)', // Unique serial number
        'Title' => 'Varchar(255)',
        'Summary' => 'Varchar(255)',
        'Description' => 'Text',
        'Collector' => 'Varchar(255)',
        'Explanations' => 'HTMLText',
        // Permession Level
        "CanViewType" => "Enum('Anyone, LoggedInUsers, OnlyTheseUsers', 'Anyone')",
        "CanEditType" => "Enum('LoggedInUsers, OnlyTheseUsers', 'OnlyTheseUsers')",
    ];
    private static $has_one = [
        'Image' => Image::class,
    ];
    private static $has_many = [
        'OtherImages' => CollectableImage::class,
    ];
    private static $many_many = [
        "ViewerGroups" => Group::class,
        "EditorGroups" => Group::class,
        "ViewerMembers" => Member::class,
        "EditorMembers" => Member::class,
    ];
    private static $defaults = [
        "CanViewType" => "Anyone",
        "CanEditType" => "OnlyTheseUsers"
    ];
    private static $searchable_fields = [
        'Title' => [
            'field' => TextField::class,
            'filter' => 'PartialMatchFilter',
        ],
        'Summary' => [
            'field' => TextField::class,
            'filter' => 'PartialMatchFilter',
        ],
        'Description' => [
            'field' => TextField::class,
            'filter' => 'PartialMatchFilter',
        ],
    ];
    private static $summary_fields = [
        'Image',
        'Title',
        'Summary',
        'Description',
    ];
    private static $extensions = [
        Versioned::class . '.versioned',
    ];
    private static $versioned_gridfield_extensions = true;
    private static $cache_permissions = [];

    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);

        $labels['Image'] = _t('Collectors.FRONT_IMAGE', 'Front Image');
        $labels['Image.StripThumbnail'] = _t('Collectors.FRONT_IMAGE', 'Front Image');
        $labels['OtherImages'] = _t('Collectors.OTHER_IMAGES', 'Other Images');

        $labels['SerialNumber'] = _t('Collectors.SERIAL_NUMBER', 'Serial Number');
        $labels['Title'] = _t('Collectors.TITLE', 'Title');
        $labels['Description'] = _t('Collectors.DESCRIPTION', 'Description');
        $labels['Explanations'] = _t('Collectors.EXPLANATIONS', 'Explanations');
        $labels['Summary'] = _t('Collectors.SUMMARY', 'Summary');
        $labels['Collector'] = _t('Collectors.COLLECTOR', 'Collector');

        $labels['Collections'] = _t('Collectors.COLLECTIONS', 'Collections');
        $labels['People'] = _t('Collectors.PEOPLE', 'People');
        $labels['Towns'] = _t('Collectors.TOWNS', 'Towns');

        // Settings
        $labels['CanViewType'] = _t('Collectors.CAN_VIEW_TYPE', 'Who can view this person');
        $labels['CanEditType'] = _t('Collectors.CAN_EDIT_TYPE', 'Who can edit this person');

        return $labels;
    }

    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $detailsTab = new Tab('Details', _t('Collectors.DETAILS', 'Details'));
        $fields->insertAfter('Main', $detailsTab);

        if ($field = $fields->fieldByName('Root.Main.Image')) {
            $field->getValidator()->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif']);
            $field->setFolderName("collectors");
        }

        $this->reorderField($fields, 'Image', 'Root.Main', 'Root.Main');

        $this->reorderField($fields, 'Title', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Summary', 'Root.Main', 'Root.Main');
        $this->reorderField($fields, 'Description', 'Root.Main', 'Root.Main');

        $this->reorderField($fields, 'SerialNumber', 'Root.Main', 'Root.Details');
        $this->reorderField($fields, 'Explanations', 'Root.Main', 'Root.Details');
        $this->reorderField($fields, 'Collector', 'Root.Main', 'Root.Details');

        $this->getPrivacyFields($fields);

        return $fields;
    }

    public function getPrivacyFields(&$fields) {
        // Prepare groups and members lists
        $groupsMap = [];
        foreach (Group::get() as $group) {
            // Listboxfield values are escaped, use ASCII char instead of &raquo;
            $groupsMap[$group->ID] = $group->getBreadcrumbs(' > ');
        }
        asort($groupsMap);

        $membersMap = [];
        foreach (Member::get() as $member) {
            // Listboxfield values are escaped, use ASCII char instead of &raquo;
            $membersMap[$member->ID] = $member->getTitle();
        }
        asort($membersMap);

        // Prepare Options
        $viewersOptionsSource = [
            "Anyone" => _t('Archives.ACCESSANYONE', "Anyone"),
            "LoggedInUsers" => _t('Archives.ACCESSLOGGEDIN', "All Logged-in users"),
            "OnlyTheseUsers" => _t('Archives.ACCESSONLYTHESE', "Only these people (choose from list)")
        ];

        $editorsOptionsSource = [
            "LoggedInUsers" => _t('Archives.ACCESSLOGGEDIN', "All Logged-in users"),
            "OnlyTheseUsers" => _t('Archives.ACCESSONLYTHESE', "Only these people (choose from list)")
        ];

        // Remove existing fields
        $fields->removeFieldFromTab('Root.Main', 'CanViewType');
        $fields->removeFieldFromTab('Root.ViewerGroups', 'ViewerGroups');
        $fields->removeFieldFromTab('Root', 'ViewerGroups');
        $fields->removeFieldFromTab('Root.ViewerMembers', 'ViewerMembers');
        $fields->removeFieldFromTab('Root', 'ViewerMembers');

        $fields->removeFieldFromTab('Root.Main', 'CanViewType');
        $fields->removeFieldFromTab('Root.EditorGroups', 'EditorGroups');
        $fields->removeFieldFromTab('Root', 'EditorGroups');
        $fields->removeFieldFromTab('Root.EditorMembers', 'EditorMembers');
        $fields->removeFieldFromTab('Root', 'EditorMembers');

        // Prepare Privacy tab
        $privacyTab = new Tab('PrivacyTab', _t('Archives.PRIVACY', 'Privacy'));
        $fields->insertAfter('OtherImages', $privacyTab);

        $fields->addFieldsToTab('Root.PrivacyTab', [
            OptionsetField::create(
                    "CanViewType", _t('Archives.CAN_VIEW_TYPE', 'Who can view this person?')
            )->setSource($viewersOptionsSource), //
            ListboxField::create("ViewerGroups", _t('Archives.VIEWER_GROUPS', "Viewer Groups"))
                    ->setSource($groupsMap)
                    ->setAttribute('data-placeholder', _t('Archives.GROUP_PLACEHOLDER', 'Click to select group')), //
            ListboxField::create("ViewerMembers", _t('Archives.VIEWER_MEMBERS', "Viewer Users"))
                    ->setSource($membersMap)
                    ->setAttribute('data-placeholder', _t('Archives.MEMBER_PLACEHOLDER', 'Click to select user')), //
            OptionsetField::create(
                    "CanEditType", _t('Archives.CAN_EDIT_TYPE', 'Who can edit this person?')
            )->setSource($editorsOptionsSource), //
            ListboxField::create("EditorGroups", _t('Archives.EDITOR_GROUPS', "Editor Groups"))
                    ->setSource($groupsMap)
                    ->setAttribute('data-placeholder', _t('Archives.GROUP_PLACEHOLDER', 'Click to select group')), //
            ListboxField::create("EditorMembers", _t('Archives.EDITOR_MEMBERS', "Editor Users"))
                    ->setSource($membersMap)
                    ->setAttribute('data-placeholder', _t('Archives.MEMBER_PLACEHOLDER', 'Click to select user'))
        ]);
    }

    function Link($action = null) {
        $page = CollectablesPage::get()
                ->filter([
                    'Collection' => $this->ClassName
                ])
                ->first();

        return $page ? $page->Link($action) : null;
    }

    /**
     * Show this DataObejct in the sitemap.xml
     */
    function AbsoluteLink($action = null) {
        return Director::absoluteURL($this->Link("show/$this->ID"));
    }

    public function Subtitle() {
        $subtitle = '';
        if ($this->Title) {
//            $subtitle = $this->Title;
        }

        return $subtitle;
    }

    function TheDate() {
        return $this->Year ? $this->Year . ' ' . $this->Calendar : null;
    }

    /// Permissions ///
    public function canCreate($member = null, $context = []) {
        if (!$this->isCreatable()) {
            return false;
        }

        if (!$member) {
            $member = Member::currentUserID();
        }

        if ($member && is_numeric($member)) {
            $member = DataObject::get_by_id(Member::class, $member);
        }

        $cachedPermission = self::cache_permission_check('create', $member, $this->ID);
        if (isset($cachedPermission)) {
            return $cachedPermission;
        }

        if ($member && Permission::checkMember($member, "ADMIN")) {
            return true;
        }

        $collectorsGroup = DataObject::get_one(Group::class, "Code = 'collectors'");
        if ($member->inGroup($collectorsGroup)) {
            return true;
        }

        $extended = $this->extendedCan('canCreateCollectables', $member);
        if ($extended !== null) {
            return $extended;
        }

        return false;
    }

    public function canView($member = null) {
        if (!$member) {
            $member = Member::currentUserID();
        }

        if ($member && is_numeric($member)) {
            $member = DataObject::get_by_id(Member::class, $member);
        }

        $cachedPermission = self::cache_permission_check('view', $member, $this->ID);
        if (isset($cachedPermission)) {
            return $cachedPermission;
        }

        if ($this->canEdit($member)) {
            return self::cache_permission_check('view', $member, $this->ID, true);
        }

        $extended = $this->extendedCan('canViewCollectables', $member);
        if ($extended !== null) {
            return self::cache_permission_check('view', $member, $this->ID, $extended);
        }

        if (!$this->CanViewType || $this->CanViewType == 'Anyone') {
            return self::cache_permission_check('view', $member, $this->ID, true);
        }

        // check for any logged-in users
        if ($this->CanViewType === 'LoggedInUsers' && $member) {
            return self::cache_permission_check('view', $member, $this->ID, true);
        }

        // check for specific groups && users
        if ($this->CanViewType === 'OnlyTheseUsers' && $member && ($member->inGroups($this->ViewerGroups()) || $this->ViewerMembers()->byID($member->ID))) {
            return self::cache_permission_check('view', $member, $this->ID, true);
        }

        return self::cache_permission_check('view', $member, $this->ID, false);
    }

    public function canDelete($member = null) {
        if (!$member) {
            $member = Member::currentUserID();
        }

        if ($member && is_numeric($member)) {
            $member = DataObject::get_by_id(Member::class, $member);
        }

        $cachedPermission = self::cache_permission_check('delete', $member, $this->ID);
        if (isset($cachedPermission)) {
            return $cachedPermission;
        }

        if ($member && Permission::checkMember($member, "ADMIN")) {
            return true;
        }

        $extended = $this->extendedCan('canDeleteCollectables', $member);
        if ($extended !== null) {
            return $extended;
        }

        return false;
    }

    public function canEdit($member = null) {
        if (!$member) {
            $member = Member::currentUserID();
        }

        if ($member && is_numeric($member)) {
            $member = DataObject::get_by_id(Member::class, $member);
        }

        $cachedPermission = self::cache_permission_check('edit', $member, $this->ID);
        if (isset($cachedPermission)) {
            return $cachedPermission;
        }

        if ($member && Permission::checkMember($member, "ADMIN")) {
            return self::cache_permission_check('edit', $member, $this->ID, true);
        }

        $collectorsGroup = DataObject::get_one(Group::class, "Code = 'collectors'");
        if ($member && $member->inGroup($collectorsGroup)) {
            return true;
        }

        if ($member && $this->hasMethod('CreatedBy') && $member == $this->CreatedBy()) {
            return self::cache_permission_check('edit', $member, $this->ID, true);
        }

        $extended = $this->extendedCan('canEditCollectables', $member);
        if ($extended !== null) {
            return self::cache_permission_check('edit', $member, $this->ID, $extended);
        }

        // check for any logged-in users with CMS access
        if ($this->CanEditType === 'LoggedInUsers' && Permission::checkMember($member, $this->config()->required_permission)) {
            return self::cache_permission_check('edit', $member, $this->ID, true);
        }

        // check for specific groups
        if ($this->CanEditType === 'OnlyTheseUsers' && $member && ($member->inGroups($this->EditorGroups()) || $this->EditorMembers()->byID($member->ID))) {
            return self::cache_permission_check('edit', $member, $this->ID, true);
        }

        return self::cache_permission_check('edit', $member, $this->ID, false);
    }

    public function isCreatable() {
        return false;
    }

    public static function cache_permission_check($typeField, $member, $personID, $result = null) {
        if (!$member) {
            $member = Member::currentUserID();
        }

        if ($member && is_numeric($member)) {
            $member = DataObject::get_by_id(Member::class, $member);
        }

        $memberID = $member ? $member->ID : '?';

        // This is the name used on the permission cache
        // converts something like 'CanEditType' to 'edit'.
        $cacheKey = strtolower($typeField) . "-$memberID-$personID";

        if (isset(self::$cache_permissions[$cacheKey])) {
            $cachedValues = self::$cache_permissions[$cacheKey];
            return $cachedValues;
        }

        self::$cache_permissions[$cacheKey] = $result;

        return self::$cache_permissions[$cacheKey];
    }

    //////// ManageableDataObject //////// 
    public function getObjectItem() {
        return $this->renderWith('Includes/Collectable_Item');
    }

    public function getObjectImage() {
        return $this->Image();
    }

    public function getObjectEditableImageName() {
        return 'Image';
    }

    public function getObjectDefaultImage() {
        return "hudhaifas/silverstripe-collectors: res/images/default-stamp.png";
    }

    public function getObjectLink() {
        return $this->Link("show/$this->ID");
    }

    public function getObjectEditLink() {
        return $this->Link("edit/$this->ID");
    }

    public function getObjectRelated() {
        $list = $this->get()
                ->filter([
                    'ID:ExactMatch:not' => $this->ID
                ])
                ->filterByCallback(function($record) {
                    return $record->canView();
                })
                ->sort('RAND()');

        return $list->count() ? $list : null;
    }

    public function getObjectSummary() {
        $lists = [];

        if ($this->Subtitle()) {
            $lists[] = [
                'Value' => $this->Subtitle()
            ];
        }

        if ($this->Summary) {
            $lists[] = [
                'Value' => $this->Summary
            ];
        }

        return new ArrayList($lists);
    }

    public function getObjectNav() {
        
    }

    public function getObjectTabs() {
        $lists = [];

        if ($this->OtherImages()->Count()) {
            $lists[] = [
                'Title' => _t('Collectors.OTHER_IMAGES', 'Other Images'),
                'Content' => $this->renderWith('Includes/Tab_OtherImages')
            ];
        }

        if ($this->Explanations) {
            $lists[] = [
                'Title' => _t('Collectors.EXPLANATIONS', 'Explanations'),
                'Content' => $this->Explanations
            ];
        }

        $this->extend('extraTabs', $lists);

        return new ArrayList($lists);
    }

    public function isObjectDisabled() {
        return !$this->canView();
    }

    public function getObjectTitle() {
        $title = '';

        if ($this->Title) {
            $title = $this->Title;
        }

        return $title;
    }

    public function canPublicView() {
        return $this->canView();
    }

    //////// SearchableDataObject //////// 
    public function getObjectRichSnippets() {
        $schema = [];

        $schema['@type'] = "Thing";
        $schema['image'] = $this->Image()->URL;
        $schema['name'] = $this->Title;

        return $schema;
    }

    //////// SociableDataObject //////// 
    public function getSocialDescription() {
        if ($this->Summary) {
            return $this->Summary;
        } elseif ($this->Description) {
            return strip_tags($this->Description);
        } elseif ($this->Explanations) {
            return strip_tags($this->Explanations);
        }

        return $this->getObjectTitle();
    }

    /// Utils ///
    function reorderField($fields, $name, $fromTab, $toTab, $disabled = false) {
        $field = $fields->fieldByName($fromTab . '.' . $name);

        if ($field) {
            $fields->removeFieldFromTab($fromTab, $name);
            $fields->addFieldToTab($toTab, $field);

            if ($disabled) {
                $field = $field->performDisabledTransformation();
            }
        }

        return $field;
    }

    function removeField($fields, $name, $fromTab) {
        $field = $fields->fieldByName($fromTab . '.' . $name);

        if ($field) {
            $fields->removeFieldFromTab($fromTab, $name);
        }

        return $field;
    }

    function trim($field) {
        if ($this->$field) {
            $this->$field = trim($this->$field);
        }
    }

    public function toString() {
        return $this->getTitle();
    }

}
