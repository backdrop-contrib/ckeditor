<?php

/**
 * CKEditor - The text editor for the Internet - http://ckeditor.com
 * Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 *
 * == BEGIN LICENSE ==
 *
 * Licensed under the terms of any of the following licenses of your
 * choice:
 *
 *  - GNU General Public License Version 2 or later (the "GPL")
 *    http://www.gnu.org/licenses/gpl.html
 *
 *  - GNU Lesser General Public License Version 2.1 or later (the "LGPL")
 *    http://www.gnu.org/licenses/lgpl.html
 *
 *  - Mozilla Public License Version 1.1 or later (the "MPL")
 *    http://www.mozilla.org/MPL/MPL-1.1.html
 *
 * == END LICENSE ==
 *
 * @file
 * CKEditor Module for Backdrop 1.x
 *
 * This file is required by the CKEeditor module if you want to enable CKFinder,
 * an advanced Ajax file browser.
 *
 */
$GLOBALS['devel_shutdown'] = FALSE;

if (!function_exists('ob_list_handlers') || ob_list_handlers()) {
  @ob_end_clean();
}

$ckfinder_user_files_path = '';
$ckfinder_user_files_absolute_path = '';

function CheckAuthentication() {
  static $authenticated;

  if (!isset($authenticated)) {
    if (!empty($_SERVER['SCRIPT_FILENAME'])) {
      $backdrop_path = dirname(dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME']))));
      if (!file_exists($backdrop_path . '/includes/bootstrap.inc')) {
        $backdrop_path = dirname(dirname(dirname($_SERVER['SCRIPT_FILENAME'])));
        $depth = 2;
        do {
          $backdrop_path = dirname($backdrop_path);
          $depth++;
        } while (!($bootstrap_file_found = file_exists($backdrop_path . '/includes/bootstrap.inc')) && $depth < 10);
      }
    }

    if (!isset($bootstrap_file_found) || !$bootstrap_file_found) {
      $backdrop_path = '../../../../..';
      if (!file_exists($backdrop_path . '/includes/bootstrap.inc')) {
        $backdrop_path = '../..';
        do {
          $backdrop_path .= '/..';
          $depth = substr_count($backdrop_path, '..');
        } while (!($bootstrap_file_found = file_exists($backdrop_path . '/includes/bootstrap.inc')) && $depth < 10);
      }
    }
    if (!isset($bootstrap_file_found) || $bootstrap_file_found) {
      $current_cwd = getcwd();
      chdir($backdrop_path);
      if (!defined('BACKDROP_ROOT')) {
        define('BACKDROP_ROOT', $backdrop_path);
      }
      require_once BACKDROP_ROOT . '/includes/bootstrap.inc';
      backdrop_bootstrap(BACKDROP_BOOTSTRAP_FULL);
      $authenticated = user_access('allow CKFinder file uploads');
      if (isset($_GET['id'], $_SESSION['ckeditor'][$_GET['id']]['UserFilesPath'], $_SESSION['ckeditor'][$_GET['id']]['UserFilesAbsolutePath'])) {
        $_SESSION['ckeditor']['UserFilesPath'] = $_SESSION['ckeditor'][$_GET['id']]['UserFilesPath'];
        $_SESSION['ckeditor']['UserFilesAbsolutePath'] = $_SESSION['ckeditor'][$_GET['id']]['UserFilesAbsolutePath'];
      }
      chdir($current_cwd);
    }
  }

  return $authenticated;
}

CheckAuthentication();

if (isset($_SESSION['ckeditor']['UserFilesPath'], $_SESSION['ckeditor']['UserFilesAbsolutePath'])) {
  $baseUrl = $_SESSION['ckeditor']['UserFilesPath'];
  // To deal with multiple application servers it's better to let CKFinder guess the server path based on the URL,
  // because the server side path changes on each request (#2127467).
  if (isset($_SERVER['PANTHEON_ENVIRONMENT'])) {
    $baseDir = resolveUrl($baseUrl);
  }
  else {
    $baseDir = $_SESSION['ckeditor']['UserFilesAbsolutePath'];
  }
}
else {
  // Nothing in session? Shouldn't happen... anyway let's try to upload it in the (almost) right place
  // Path to user files relative to the document root.
  $baseUrl = strtr(base_path(), array(
        '/modules/ckeditor/ckfinder/core/connector/php' => '',
      )) . config_get('system.core','file_private_path') . '/';
  $baseDir = resolveUrl($baseUrl);
}
