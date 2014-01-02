Google Authenticator for Symfony2
=================================

Small Symfony2 project that's built to show how Google Authenticator (Two Factor Authentication)
can be plugged into a project.

I've followed the blog post at http://www.christianscheb.de/archives/302

1) Install Vendors
==================

    php composer.phar update

2) Make Cache & Logs Writable
==============================

    chmod +w -R app/cache
    chmod +w -R app/logs

3) Generate Database & Tables
-----------------------------
    php app/console doctrine:database:create
    php app/console doctrine:schema:update --force