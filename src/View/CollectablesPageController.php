<?php

/**
 *
 * @author Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, Jan 27, 2017 - 11:24:42 AM
 */
class CollectablesPageController
        extends DataObjectPageController {

    protected function getObjectsList() {
        return DataObject::get($this->Collection)
                        ->filterByCallback(function($record) {
                            return $record->canView();
                        });
    }

    protected function searchObjects($list, $keywords) {
        return $list->filterAny([
                    'Title:PartialMatch' => $keywords,
                    'Summary:PartialMatch' => $keywords,
                    'Description:PartialMatch' => $keywords,
                    'Country:PartialMatch' => $keywords,
                    'Year:PartialMatch' => $keywords,
                    'SerialNumber:PartialMatch' => $keywords,
                    'Collector:PartialMatch' => $keywords,
        ]);
    }

    protected function getFiltersList() {
        return null;
    }

    protected function getPageLength() {
        return 24;
    }

}
