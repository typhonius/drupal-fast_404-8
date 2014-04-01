<?php

namespace Drupal\fast404\EventSubscriber;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Component\Utility\Settings;
use Drupal\fast404\Fast404;

class Fast404EventSubscriber implements EventSubscriberInterface {
  public $event;

  public function onKernelRequest(GetResponseEvent $event) {
    $this->event = $event;
    drupal_set_message('fast 404 check');

    $fast_404 = new Fast404;

    $fast_404->extensionCheck();

    $fast_404->patchCheck();

//    //require_once('fast404.inc');
//
//    // If the file extension wasn't set to be checked in settings.php, do it
//    // here.
//    if (!defined('FAST_404_EXT_CHECKED')) {
//      // This function has an exit in it, so it will end the page if needed.
//      fast_404_ext_check();
//    }
//
//    // Don't do a full check if the path was already checked in settings.php.
//    if (defined('FAST_404_PATH_CHECKED')) {
//      return TRUE;
//    }
//
//    // If the path is invalid then return the Fast 404 html
//    fast_404_path_check();
//
//    if (Settings::get('fast_404_extension_check')) {
//
//    }
    //$event->stopPropagation();
  }

  static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('onKernelRequest', 100);
    return $events;
  }

}
