<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, Feb 1, 2017 - 1:08:19 PM
 */
class CollectableExtension
        extends DataExtension {

    private static $belongs_many_many = array(
        'Collectables' => 'Collectable',
    );

    public function updateCMSFields(FieldList $fields) {
        
    }

    public function extraTabs(&$lists) {
        $collectables = $this->owner->Collectables();
        if ($collectables->Count()) {
            $lists[] = array(
                'Title' => _t('Collectors.COLLECTABLES', 'Collectables'),
                'Content' => $this->owner
                        ->customise(array(
                            'Results' => $collectables->sort('Name ASC')
                        ))
                        ->renderWith('List_Grid')
            );
        }
    }

}