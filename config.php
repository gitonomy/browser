<?php return array(
    'gitonomy-server'  => new Gitonomy\Git\Repository('/var/www/gitonomy.dev'),
    'gitonomy-browser' => new Gitonomy\Git\Repository('/var/www/gitonomy-browser.dev'),
    'gitlib'           => new Gitonomy\Git\Repository('/var/www/gitonomy-browser.dev/vendor/gitonomy/gitlib'),
    'alom'             => new Gitonomy\Git\Repository('/var/www/alom.dev'),
    'php-webdriver'    => new Gitonomy\Git\Repository('/var/www/php-webdriver.dev'),
);
