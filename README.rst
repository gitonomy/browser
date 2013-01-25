Gitonomy browser
================

Gitonomy browser is a handy tool to visualize your local git repositories.

It's still a work in progress, feel free to `contribute on github`_.

Built with `Silex`_ and `gitlib`_ (PHP).

Installation
............

To install project, you first need to clone is using ``git``. When project is
cloned, you will need to configure it. To do so, go to ``config`` folder and
copy distributed files:

.. code-block:: bash

    git clone git@github.com:gitonomy/browser.git gitonomy-browser
    cd gitonomy-browser/config
    cp dev.php-dist dev.php
    cp prod.php-dist prod.php

Edit ``prod.php`` and configure where your git repositories are located. You
can configure it in 3 different ways.

First way to configure is to give an exhaustive list of repositories you want:

.. code-block:: php

    <?php # prod.php

    $app['repositories'] = array(
        'foobar' => new Gitonomy\Git\Repository('/var/www/foobar'),
        'barbaz' => new Gitonomy\Git\Repository('/var/www/barbaz'),
    );

The second way is to use recursive function to detect repositories. It's very
useful if you have multi-level folders:

.. code-block:: php

    <?php # prod.php

    $app['repositories'] = ';


When you have filled it, launch `composer`_ to fetch dependencies and make
project workable:

.. code-block:: bash

    php composer.phar install

.. _composer: http://packagist.org
.. _silex: http://silex.sensiolabs.org/
.. _gitlib: https://github.com/gitonomy/gitlib
.. _contribute on github: https://help.github.com/articles/fork-a-repo
