<?php

namespace Drupal\fast_404\EventSubscriber;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Fast404EventSubscriber implements EventSubscriberInterface {

  public function onKernelRequestCheck404Page(GetResponseEvent $event) {
    drupal_set_message('fast 404 check');
  }

  static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('onKernelRequestCheck404Page', 100);
    return $events;
  }

}
