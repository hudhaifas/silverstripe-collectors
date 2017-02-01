<?php

/**
 * Fetches the name of the current module folder name.
 *
 * @return string
 */
if (!defined('COLLECTORS_DIR')) {
    define('COLLECTORS_DIR', ltrim(Director::makeRelative(realpath(__DIR__)), DIRECTORY_SEPARATOR));
}