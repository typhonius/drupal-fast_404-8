
OMG! Fast_404 is Super Fast and Super Amazing. It is also very aggressive and hard-core.

The code below needs to go into your settings.php. Read the comments carefully before enabling any non-default behavior.


--- Copy the code below into the BOTTOM of your settings.php, unless you are using cacherouter. Put this above cacherouter ---

/**
* Fast 404 settings:
* 
* Fast 404 will do two seperate types of 404 checking. 
* 
* The first is to check for URLs which appear to be files or images. If Drupal 
* is handling these items, then they were not found in the file system and are a 404.
* 
* The second is to check whether or not the URL exists in Drupal by checking with
* the menu router, aliases and redirects. If the page does not exist, we will server a fast 404 error and exit.
* 
*/

# Load the fast_404.inc file. This is needed if you wish to do extension checking in settings.php
include_once('./sites/all/modules/fast_404/fast_404.inc');

# Disallowed extensions. Any extension in here will not be served by Drupal and will get a fast 404
# Default extension list, this is considered safe and is even in queue for Drupal 8 (see: http://drupal.org/node/76824)
$conf['fast_404_exts'] = '/.(txt|png|gif|jpe?g|css|js|ico|swf|flv|cgi|bat|pl|dll|exe|asp)$/i';

# Extension list requiring whitelisting to be activated **If you use this without whitelisting enabled your site will not load!
//$conf['fast_404_exts'] = '/.(txt|png|gif|jpe?g|css|js|ico|swf|flv|cgi|bat|pl|dll|exe|asp|php|html?|xml)$/i';

# Default fast 404 error message.
$conf['fast_404_html'] = '<html xmlns="http://www.w3.org/1999/xhtml"><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL "@path" was not found on this server.</p></body></html>';

# Check paths during bootstrap and see if they are legitimate
$conf['fast_404_path_check'] = FALSE;

# If enabled, you may add extensions such as xml and php to the $conf['fast_404_exts'] above. 
# BE CAREFUL with this setting as some modules use their own php files and you need to be certain 
# they do not bootstrap Drupal. If they do, you will need to whitelist them too.
$conf['fast_404_url_whitelisting'] = FALSE;

# Array of whitelisted files/urls. Used if whitelisting is set to TRUE
$conf['fast_404_whitelist'] = array('index.php', 'rss.xml', 'install.php', 'cron.php', 'update.php', 'xmlrpc.php');

# Call the extension checking now. This will skip any logging of 404s.
# Extension checking is safe to do from settings.php. There are many examples of this on Drupal.org.
//fast_404_ext_check();

# Path checking. USE AT YOUR OWN RISK (only works with MySQL)
# Path checking at this phase is more dangerous, but faster. Normally Fast_404 will check paths during Drupal
# boostrap via hook_boot. Checking paths here is faster, but trickier as the Drupal database connections have not
# yet been made and the module must make a separate DB connection. Under most configurations this DB connection will be
# reused by Drupal so there is no waste.
# While this setting finds 404s faster, it adds a bit more load time to regular pages, so only use
# if you are spending too much CPU/Memory/DB on 404s and the trade-off is worth it.
# This setting will deliver 404s with less than 2MB of RAM.
//fast_404_path_check();