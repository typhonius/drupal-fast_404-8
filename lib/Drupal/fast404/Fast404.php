<?php

namespace Drupal\fast404;

class Fast404 {

  // TODO set variables for whether it's checked or not

  public function extensionCheck() {
    // Get the request Drupal needs to respond to in order to determine whether
    // this is a 404 path.
    $request = \Drupal::service('request')->query->get('q', '/');
    var_dump($request);
  }

  public function pathCheck() {
    drupal_set_message('fast404 check agan');
  }
}