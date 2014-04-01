<?php

namespace Drupal\fast404;

class Fast404 {

  // TODO set variables for whether it's checked or not

  public function extensionCheck() {
    drupal_set_message('fast404');
    $request = \Drupal::service('request')->query->get('q', '/');
    var_dump($request);
  }

  public function patchCheck() {
    drupal_set_message('fast404 check agan');
  }
}