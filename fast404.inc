<?php

use Drupal\Component\Utility\Settings;
use Symfony\Component\HttpFoundation\Request;

function fast404_preboot(Array $settings = array()) {

  // We must instantiate a Settings object before attempting to Settings::get().
  new Settings($settings);

  // Load the Fast404 class from within the module directory. Since modules
  // haven't been loaded at this point we cannot use the autoloader.
  require_once __DIR__ . '/lib/Drupal/fast404/Fast404.php';

  // Create a request object so we have something to pass Fast404.
  $request = Request::createFromGlobals();

  // This is pretty much a wrote copy of the Fast404EventSubscriber class that
  // fires when the module is enabled.

  $fast_404 = new \Drupal\fast404\Fast404($request);

  $fast_404->extensionCheck();
  if ($fast_404->isPathBlocked()) {
    $fast_404->response();
  }

  $fast_404->pathCheck();
  if ($fast_404->isPathBlocked()) {
    $fast_404->response();
  }
}