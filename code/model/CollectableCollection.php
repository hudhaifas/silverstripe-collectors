<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, Dec 4, 2016 - 12:42:27 AM
 */
class CollectableCollection
        extends DataObject {

    private static $db = array(
        'Title' => 'Varchar(255)',
    );
    private static $has_one = array(
    );
    private static $has_many = array(
    );
    private static $belongs_many_many = array(
        'Collectables' => 'Collectable',
    );
    private static $searchable_fields = array(
        'Title',
    );
    private static $summary_fields = array(
        'Title',
        'Collectables.Count',
    );

    public function fieldLabels($includerelations = true) {
        $labels = parent::fieldLabels($includerelations);

        $labels['Title'] = _t('Collector.TITLE', 'Title');
        $labels['Collectables.Count'] = _t('Collector.NUMBER_OF_COLLECTABLE', 'Number Of Collectables');

        return $labels;
    }

}
