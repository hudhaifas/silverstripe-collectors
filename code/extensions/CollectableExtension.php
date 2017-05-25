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
        $documents = $collectables->filter('ClassName', 'CollectableDocument');
        $articles = $collectables->filter('ClassName', 'CollectableArticle');
        $photos = $collectables->filter('ClassName', 'CollectablePhoto');
        $artworks = $collectables->filter('ClassName', 'CollectableArtwork');

        $this->insertExtraTab($lists, $documents, _t('Collectors.DOCUMENTS', 'Documents'));
        $this->insertExtraTab($lists, $articles, _t('Collectors.ARTICLES', 'Articles'));
        $this->insertExtraTab($lists, $photos, _t('Collectors.PHOTOS', 'Photos'));
        $this->insertExtraTab($lists, $artworks, _t('Collectors.ARTWORKS', 'Artworks'));
//        $this->insertExtraTab($lists, $collectables, _t('Collectors.COLLECTABLES', 'Collectables'));
    }

    private function insertExtraTab(&$lists, $list, $title) {
        if ($list->Count()) {
            $lists[] = array(
                'Title' => $title,
                'Content' => $this->owner
                        ->customise(array(
                            'Results' => $list->sort('Title ASC')
                        ))
                        ->renderWith('List_Grid')
            );
        }
    }

}
