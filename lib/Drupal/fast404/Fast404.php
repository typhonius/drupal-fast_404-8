<?php

namespace Drupal\fast404;

use Drupal\Component\Utility\Settings;
use Drupal\Core\Database\Database;
use Drupal\Component\Utility\String;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Fast404 {

  public $respond_404 = FALSE;

  public $request;

  public $event;

  public function __construct(Request $request) {
    $this->request = $request;
  }

  public function extensionCheck() {
    // Get the path from the request.
    $path = $this->request->getPathInfo();

    // Ignore calls to the homepage, to avoid unnecessary processing.
    if (!isset($path) || $path == '/') {
      return;
    }

    // Check to see if the URL is that of an image derivative.
    // If this file does not already exist, it will be handled via Drupal.
    if (strpos($path, 'styles/')) {

      // Check to see if we will allow anon users to access this page.
      if (!Settings::get('fast404_allow_anon_imagecache', TRUE)) {
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

      // We're allowing anyone to hit non-existing image derivative URLs
      // (default behavior).
      else {
        return;
      }
    }

    // If we are using URL whitelisting then determine if the current URL is
    // whitelisted before running the extension check.

    // Check for exact URL matches and assume it's fine if we get one.
    $url_whitelist = Settings::get('fast404_url_whitelisting', FALSE);
    if (is_array($url_whitelist)) {
      if (in_array($path, $url_whitelist)) {
        return;
      }
    }

    // Check for whitelisted strings in the URL.
    $string_whitelist = Settings::get('fast404_string_whitelisting', FALSE);
    if (is_array($string_whitelist)) {
      foreach ($string_whitelist as $str) {
        if (strstr($path, $str) !== FALSE) {
          return;
        }
      }
    }

    $extensions =  Settings::get('fast404_exts', '/^(?!robots).*\.(txt|png|gif|jpe?g|css|js|ico|swf|flv|cgi|bat|pl|dll|exe|asp)$/i');
    // Determine if URL contains a blacklisted extension.
    if (isset($extensions) && preg_match($extensions, $path, $m)) {
      $this->blockPath();
      return;
    }

  }

  public function pathCheck() {
    // Since the path check is a lot more aggressive in its blocking we should
    // actually check that the user wants it to be done.
    if (!Settings::get('fast404_path_check', FALSE)) {
      return;
    }
    // Get the path from the request.
    $path = $this->request->getPathInfo();

    // Ignore calls to the homepage, to avoid unnecessary processing.
    if (!isset($path) || $path == '/') {
      return;
    }

    // If we have a database connection we can use it, otherwise we might be
    // initialising it.

    // todo document that drupalkernel is after config atm but there is a d.o to make it before
    // We remove '/' from the list of possible patterns as it exists in the router
    // by default. This means that the query would match any path (/%) which is
    // undesireable when we're only looking to match some paths.
    $sql = "SELECT pattern_outline FROM {router} WHERE :path LIKE CONCAT(pattern_outline, '%') AND pattern_outline != '/'";
    $result = Database::getConnection()->query($sql, array(':path' => $path))->fetchField();
    if ($result) {
      return;
    }

    // Check the URL alias table for anything that's not a standard Drupal path.
    $sql = "SELECT pid FROM {url_alias} WHERE :alias = CONCAT('/', alias)";
    $result = Database::getConnection()->query($sql, array(':alias' => $path))->fetchField();
    if ($result) {
      return;
    }

    // If we get to here it means nothing has matched the request so we assume
    // it's a bad path and block it.
    $this->blockPath();

  }

  public function blockPath() {
    $this->respond_404 = TRUE;
  }

  public function isPathBlocked() {
    return $this->respond_404;
  }

  public function response() {
    $message = Settings::get('fast404_html', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL "@path" was not found on this server (Fast 404).</p></body></html>');
    $response = new Response(String::format($message, array('@path' => $this->request->getPathInfo())), 404);
    $response->send();
  }
}
