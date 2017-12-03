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
 * @version 1.0, Dec 3, 2016 - 10:35:14 PM
 */
class Collectable
        extends DataObject
        implements ManageableDataObject, SearchableDataObject {

    private static $db = array(
        'SerialNumber' => 'Varchar(20)', // Unique serial number
        'Title' => 'Varchar(255)',
        'Summary' => 'Varchar(255)',
        'Description' => 'Text',
        'Collector' => 'Varchar(255)',
        'Explanations' => 'HTMLText',
        // Permession Level
        "CanViewType" => "Enum('Anyone, LoggedInUsers, OnlyTheseUsers', 'LoggedInUsers')",
        "CanEditType" => "Enum('LoggedInUsers, OnlyTheseUsers', 'OnlyTheseUsers')",
    );
    private static $has_one = array(
        'Image' => 'Image',
    );
    private static $has_many = array(
        'OtherImages' => 'CollectableImage',
    );
    private static $many_many = array(
        "ViewerGroups" => "Group",
        "EditorGroups" => "Group",
        "ViewerMembers" => "Member",
        "EditorMembers" => "Member",
    );
    private static $defaults = array(
        "CanViewType" => "LoggedInUsers",
        "CanEditType" => "OnlyTheseUsers"
    );
    private static $searchable_fields = array(
        'Title' => array(
            'field' => 'TextField',
            'filter' => 'PartialMatchFilter',
        ),
        'Summary' => array(
            'field' => 'TextField',
            'filter' => 'PartialMatchFilter',
        ),
        'Description' => array(
            'field' => 'TextField',
            'filter' => 'PartialMatchFilter',
        ),
    );
    private static $summary_fields = array(
        'Image.StripThumbnail',
        'Title',
        'Summary',
        'Description',
    );
    private static $cache_permissions = array();

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
            $field->getValidator()->setAllowedExtensions(array('jpg', 'jpeg', 'png', 'gif'));
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
        $groupsMap = array();
        foreach (Group::get() as $group) {
            // Listboxfield values are escaped, use ASCII char instead of &raquo;
            $groupsMap[$group->ID] = $group->getBreadcrumbs(' > ');
        }
        asort($groupsMap);

        $membersMap = array();
        foreach (Member::get() as $member) {
            // Listboxfield values are escaped, use ASCII char instead of &raquo;
            $membersMap[$member->ID] = $member->getTitle();
        }
        asort($membersMap);

        // Prepare Options
        $viewersOptionsSource = array(
            "Anyone" => _t('Archives.ACCESSANYONE', "Anyone"),
            "LoggedInUsers" => _t('Archives.ACCESSLOGGEDIN', "All Logged-in users"),
            "OnlyTheseUsers" => _t('Archives.ACCESSONLYTHESE', "Only these people (choose from list)")
        );

        $editorsOptionsSource = array(
            "LoggedInUsers" => _t('Archives.ACCESSLOGGEDIN', "All Logged-in users"),
            "OnlyTheseUsers" => _t('Archives.ACCESSONLYTHESE', "Only these people (choose from list)")
        );

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

        $fields->addFieldsToTab('Root.PrivacyTab', array(
            OptionsetField::create(
                    "CanViewType", _t('Archives.CAN_VIEW_TYPE', 'Who can view this person?')
            )->setSource($viewersOptionsSource), //
            ListboxField::create("ViewerGroups", _t('Archives.VIEWER_GROUPS', "Viewer Groups"))
                    ->setMultiple(true)
                    ->setSource($groupsMap)
                    ->setAttribute('data-placeholder', _t('Archives.GROUP_PLACEHOLDER', 'Click to select group')), //
            ListboxField::create("ViewerMembers", _t('Archives.VIEWER_MEMBERS', "Viewer Users"))
                    ->setMultiple(true)
                    ->setSource($membersMap)
                    ->setAttribute('data-placeholder', _t('Archives.MEMBER_PLACEHOLDER', 'Click to select user')), //
            OptionsetField::create(
                    "CanEditType", _t('Archives.CAN_EDIT_TYPE', 'Who can edit this person?')
            )->setSource($editorsOptionsSource), //
            ListboxField::create("EditorGroups", _t('Archives.EDITOR_GROUPS', "Editor Groups"))
                    ->setMultiple(true)
                    ->setSource($groupsMap)
                    ->setAttribute('data-placeholder', _t('Archives.GROUP_PLACEHOLDER', 'Click to select group')), //
            ListboxField::create("EditorMembers", _t('Archives.EDITOR_MEMBERS', "Editor Users"))
                    ->setMultiple(true)
                    ->setSource($membersMap)
                    ->setAttribute('data-placeholder', _t('Archives.MEMBER_PLACEHOLDER', 'Click to select user'))
        ));
    }

    function Link($action = null) {
        $page = CollectablesPage::get()
                ->filter(array(
                    'Collection' => $this->ClassName
                ))
                ->first();

        return $page ? $page->Link($action) : null;
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
    public function canCreate($member = false) {
        if (!$this->isCreatable()) {
            return false;
        }

        if (!$member) {
            $member = Member::currentUserID();
        }

        if ($member && is_numeric($member)) {
            $member = DataObject::get_by_id('Member', $member);
        }

        $cachedPermission = self::cache_permission_check('create', $member, $this->ID);
        if (isset($cachedPermission)) {
            return $cachedPermission;
        }

        if ($member && Permission::checkMember($member, "ADMIN")) {
            return true;
        }

        $extended = $this->extendedCan('canCreateCollectables', $member);
        if ($extended !== null) {
            return $extended;
        }

        return false;
    }

    public function canView($member = false) {
        if (!$member) {
            $member = Member::currentUserID();
        }

        if ($member && is_numeric($member)) {
            $member = DataObject::get_by_id('Member', $member);
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

    public function canDelete($member = false) {
        if (!$member) {
            $member = Member::currentUserID();
        }

        if ($member && is_numeric($member)) {
            $member = DataObject::get_by_id('Member', $member);
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

    public function canEdit($member = false) {
        if (!$member) {
            $member = Member::currentUserID();
        }

        if ($member && is_numeric($member)) {
            $member = DataObject::get_by_id('Member', $member);
        }

        $cachedPermission = self::cache_permission_check('edit', $member, $this->ID);
        if (isset($cachedPermission)) {
            return $cachedPermission;
        }

        if ($member && Permission::checkMember($member, "ADMIN")) {
            return self::cache_permission_check('edit', $member, $this->ID, true);
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
            $member = DataObject::get_by_id('Member', $member);
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
        return $this->renderWith('Collectable_Item');
    }

    public function getObjectImage() {
        return $this->Image();
    }

    public function getObjectDefaultImage() {
        return "collectors/images/default-stamp.png";
    }

    public function getObjectLink() {
        return $this->Link("show/$this->ID");
    }

    public function getObjectEditLink() {
        return $this->Link("edit/$this->ID");
    }

    public function getObjectRelated() {
        $list = $this->get()
                ->filter(array(
                    'ID:Negation' => $this->ID
                ))
                ->filterByCallback(function($record) {
                    return $record->canView();
                })
                ->sort('RAND()');

        return $list->count() ? $list : null;
    }

    public function getObjectSummary() {
        $lists = array();

        if ($this->Subtitle()) {
            $lists[] = array(
                'Value' => $this->Subtitle()
            );
        }

        if ($this->Summary) {
            $lists[] = array(
                'Value' => $this->Summary
            );
        }

        return new ArrayList($lists);
    }

    public function getObjectNav() {
        
    }

    public function getObjectTabs() {
        $lists = array();

        if ($this->OtherImages()->Count()) {
            $lists[] = array(
                'Title' => _t('Collectors.OTHER_IMAGES', 'Other Images'),
                'Content' => $this->renderWith('Tab_OtherImages')
            );
        }

        if ($this->Explanations) {
            $lists[] = array(
                'Title' => _t('Collectors.EXPLANATIONS', 'Explanations'),
                'Content' => $this->Explanations
            );
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

    //////// SearchableDataObject //////// 
    public function getObjectRichSnippets() {
        $schema = array();

        $schema['@type'] = "Thing";
        $schema['image'] = $this->Image()->URL;
        $schema['name'] = $this->Title;

        return $schema;
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
