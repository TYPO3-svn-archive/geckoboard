.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt
.. include:: Images.txt

Getting started
===============

.. contents::
   :local:
   :depth: 1


Register with Geckoboard
-----------------------------

In order to use this extension you will need a Geckoboard_ account.


Install and activate the extension
----------------------------------

Install and activate the geckoboard extension.


Extension Manager Configuration
-------------------------------

|img-3| *Abb. 1: extension configuration*

After your successful registration with geckoboard and activation of the extension,
you must enter your API key in the extension config. The API Key can be found under 'Your Account'
in the Geckoboard backend.

The field 'Date Format' lets you configure the way dates will be displayed in the text widgets.
The format must be PHP_ compatible.

If your TYPO3 installation uses any page types (DB field: doktype in the table pages) other than the standard,
you will need to enter them as a comma-separated list in the field 'Which page types should be included' if
you want them to be included in the widget data. Otherwise, leave this set to the default '1'.

Some widgets show data from X hours before the current time. With the field 'Data from the last how many hours'
you can set this to a value that makes the most sense for your installation. The default is 24 hours.

.. _Geckoboard: http://www.geckoboard.com/
.. _PHP: http://php.net/manual/en/function.date.php