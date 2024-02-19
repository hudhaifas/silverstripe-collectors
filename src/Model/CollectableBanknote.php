<?php

/**
 *
 * @author Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, Dec 3, 2016 - 11:05:17 PM
 */
class CollectableBanknote
        extends CollectableCurrency {

    public function isCreatable() {
        return true;
    }

}
