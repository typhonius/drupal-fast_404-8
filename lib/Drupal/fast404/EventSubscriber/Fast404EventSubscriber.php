<?php

namespace Drupal\fast404\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\fast404\Fast404;

class Fast404EventSubscriber implements EventSubscriberInterface {

  public function onKernelRequest(GetResponseEvent $event) {
    $request = \Drupal::service('request');

    $fast_404 = new Fast404($request);

    $fast_404->extensionCheck();
    if ($fast_404->isPathBlocked()) {
      $fast_404->response();
    }

    $fast_404->pathCheck();
    if ($fast_404->isPathBlocked()) {
      $fast_404->response();
    }
  }

  static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('onKernelRequest', 100);
    return $events;
  }

}
