Gitonomy browser
================

Installation
------------

.. code-block:: bash

    git clone git@github.com:gitonomy/browser.git /path/to/install
    cd /path/to/install
    cp config_dist.php config.php

Edit ``config.php`` and modify array to return instances of ``Repository`` objects on each of your projects:

.. code-block:: php

    <?php return array(
        'symfony' => new Gitonomy\Git\Repository('/var/www/symfony'),
        'silex' => new Gitonomy\Git\Repository('/var/www/silex'),
        'twig' => new Gitonomy\Git\Repository('/var/www/twig'),
    );

You can also map to bare repositories.

When you have filled the file, launch `composer`_ to fetch dependencies and make project workable:

.. code-block:: bash

    php composer.phar install

.. _composer: http://packagist.org
