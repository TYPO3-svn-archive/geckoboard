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


Add a scheduler task
----------------------------------

Add a scheduler task in the backend. Choose which widgets you would like to have displayed on your Geckoboard.

The currently available TYPO3 Geckoboard widgets are:

- Count of active pages

- Count or list of last changed pages

- Count or list of latest new pages

Some widgets have different types, i.e. 'Latest new pages' is available in a text list view or a count view. Per scheduler task you
can include a push for one type per widget. If you would like to add both types of a widget, just add two scheduler tasks,
one with the list and one with the count.

Now press Save. Depending on the widgets you have chosen you will see something similar to the image displayed.

|img-4| *Abb. 1: scheduler configuration*

Each type of widget must be configured with the Geckoboard widget ID and the display type of the widget. This display type must
correspond to the widget you will create in your Geckoboard account! If you're not sure what type it is, click the help link
next to the radio button text.

|img-5| *Abb. 2: choose the widget type*

You will need to set up a Geckoboard widget for each widget you choose in the scheduler configuration. This extension pushes the data to
Geckoboard, so you will need to choose the method 'PUSH'. The options will then change and you can copy the widget key out of
the Geckoboard backend and add it in the scheduler configuration.

|img-6| *Abb. 3: edit the new widget*

After you have added the widgets in Geckoboard and successfully saved the scheduler configuration, just run the task once manually and
watch the data appear on your dashboard!

