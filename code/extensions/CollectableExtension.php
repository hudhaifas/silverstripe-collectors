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
        $fields->removeFieldFromTab('Root.Collectables', 'Collectables');
        $fields->removeFieldFromTab('Root', 'Collectables');

        $collectables = $this->owner->Collectables();

        $subLists = $this->getCollectableLists();

        $documentsTab = new Tab('DocumentsTab', _t('Collectors.DOCUMENTS', 'Documents'));
        $fields->insertAfter('Main', $documentsTab);
        $fields->addFieldToTab('Root.DocumentsTab', $this->createGridField('CollectableDocuments', _t('Collectors.DOCUMENTS', 'Documents'), $subLists['Documents'], 'CollectableDocument'));

        $articlesTab = new Tab('ArticlesTab', _t('Collectors.ARTICLES', 'Articles'));
        $fields->insertAfter('Main', $articlesTab);
        $fields->addFieldToTab('Root.ArticlesTab', $this->createGridField('CollectableArticles', _t('Collectors.ARTICLES', 'Articles'), $subLists['Articles'], 'CollectableArticle'));

        $photosTab = new Tab('PhotosTab', _t('Collectors.PHOTOS', 'Photos'));
        $fields->insertAfter('Main', $photosTab);
        $fields->addFieldToTab('Root.PhotosTab', $this->createGridField('CollectablePhotos', _t('Collectors.PHOTOS', 'Photos'), $subLists['Photos'], 'CollectablePhoto'));

        $artworksTab = new Tab('ArtworksTab', _t('Collectors.ARTWORKS', 'Artworks'));
        $fields->insertAfter('Main', $artworksTab);
        $fields->addFieldToTab('Root.ArtworksTab', $this->createGridField('CollectableArtworks', _t('Collectors.ARTWORKS', 'Artworks'), $subLists['Artworks'], 'CollectableArtwork'));
    }

    private function createGridField($name, $title, $dataList, $className) {
        $field = new GridField($name, $title, $dataList);
        $field->setModelClass($className);

        $cc = $field->getConfig();
        $cc->addComponent(new GridFieldButtonRow('before'));
        $cc->removeComponentsByType('GridFieldAddNewButton');
        $cc->addComponent(new GridFieldAddNewButton());
        $cc->addComponent(new GridFieldAddExistingAutocompleter('buttons-before-right', array('SerialNumber', 'Title', 'Summary', 'Description')));
        $cc->addComponent(new GridFieldDetailForm());

        return $field;
    }

    public function extraTabs(&$lists) {
        $subLists = $this->getCollectableLists();

        $this->insertExtraTab($lists, $subLists['Documents'], _t('Collectors.DOCUMENTS', 'Documents'));
        $this->insertExtraTab($lists, $subLists['Articles'], _t('Collectors.ARTICLES', 'Articles'));
        $this->insertExtraTab($lists, $subLists['Photos'], _t('Collectors.PHOTOS', 'Photos'));
        $this->insertExtraTab($lists, $subLists['Artworks'], _t('Collectors.ARTWORKS', 'Artworks'));
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

    private function getCollectableLists() {
        $collectables = $this->owner->Collectables();

        return array(
            'Documents' => $collectables->filter('ClassName', 'CollectableDocument'),
            'Articles' => $collectables->filter('ClassName', 'CollectableArticle'),
            'Photos' => $collectables->filter('ClassName', 'CollectablePhoto'),
            'Artworks' => $collectables->filter('ClassName', 'CollectableArtwork'),
        );
    }

}
