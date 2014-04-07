<?php

namespace Drupal\fast404;
use Drupal\Component\Utility\Settings;
use Symfony\Component\HttpFoundation\Request;


class Fast404 {

  public $extension_checked;

  public $respond_404 = FALSE;

  public $path_checked;

  public $request;

  public function __construct(Request $request) {
    $this->request = $request;
  }

  // TODO set variables for whether it's checked or not

  public function extensionCheck() {

    // Get the path from the request.
    $path = $this->request->getPathInfo();

    // Ignore calls to the homepage, to avoid unnecessary processing.
    if (!isset($path) || $path == '/') {
      return;
    }

    // Check to see if the URL is an imagecache URL. If this file does not
    // already exist, it will be handled via Drupal.
    if (strpos($path, 'styles/')) {

      // Check to see if we will allow anon users to access this page.
      if (!Settings::get('fast_404_allow_anon_imagecache', TRUE)) {
        $cookies = $this->request->cookies->all();

        // At this stage of the game we don't know if the user is logged in via
        // regular function calls. Simply look for a session cookie. If we find
        // one we'll assume they're logged in
        if (isset($cookies) && is_array($cookies)) {
          foreach ($cookies as $cookie) {
            if (stristr($cookie, 'SESS')) {
              return;
            }
          }
        }
      }

      // We're allowing anyone to hit non-existing imagecache URLs (default
      // behavior).
      else {
        return;
      }
    }

    // If we are using URL whitelisting then determine if the current URL is
    // whitelisted before running the extension check.

    // Check for exact URL matches and assume it's fine if we get one.
    $url_whitelist = Settings::get('fast_404_url_whitelisting', FALSE);
    if (is_array($url_whitelist)) {
      if (in_array($path, $url_whitelist)) {
        return;
      }
    }

    // Check for whitelisted strings in the URL.
    $string_whitelist = Settings::get('fast_404_string_whitelisting', FALSE);
    if (is_array($string_whitelist)) {
      foreach ($string_whitelist as $str) {
        if (strstr($path, $str) !== FALSE) {
          return;
        }
      }
    }

    $extensions =  Settings::get('fast_404_exts', '/^(?!robots).*\.(txt|png|gif|jpe?g|css|js|ico|swf|flv|cgi|bat|pl|dll|exe|asp)$/i');
    // Determine if URL contains a blacklisted extension.
    if (isset($extensions) && preg_match($extensions, $path, $m)) {
      $this->blockPath();
      return;
    }

    $this->extension_checked = TRUE;
  }

  public function pathCheck() {
    // if we have a database connection, use it.
    $this->path_checked = TRUE;

  }

  public function blockPath() {
    $this->respond_404 = TRUE;
  }

  public function isPathBlocked() {
    return $this->respond_404;
  }
}