TCI TRANSPORTATION
Wordpress Child Theme
==================

This theme contains all custom code written for the site https://tcitransportation.com/
This theme is the same one used on https://sales.tcitransportation.com/


FILE STRUCTURE OVERVIEW
=======================
- functions.php: Various site modifications
- debug.php: Debug functions
- inc/
    - helpers.php:  Config loader & Template render function
    - contact.php:  Contact form REST endpoint
    - waf_truck_form.php:  Truck form dynamic fields
    - shortcodes.php:  Contact Information Shortcode
    - waire/:  Waire Calculator 
        - index.php: shortcode
        - template.php: template
- lib/waf
    - plugin.php
    - extras/custom-taxonomies/custom-taxonomies.php: Helper functions for creating taxonomies
- waf-json: contains json form defintions
- waf-templates: contains form html templates

* The main theme functions.php file contains various modifications made to the site.
* The debug.php file provides debugging output.
  The constant DEBUG_IP & the function d() can be used to display output only visible to the developer.
* The inc/ folder stores the CSV Importer, the Waire calculator, and other add-ons
* The lib/waf folder contains the code used to generate forms in the back-end.
   Several helper functions are used for the front end for custom taxonomies, current_url()


FILE DETAILS
============
* functions.php
---------------
Contains various site fixes.

* debug.php
-----------
- the constant "DEBUG_IP" should be defined in the functions.php file
- function d() displays output that is only visible when the site is viewed
    from "DEBUG_IP"
    - dl() outputs a line
- function is_devel() is used to check for the DEBUG_IP
- there is also an admin bar toggle to manually enable/disable debug code

** There is some chance debug code may be caught by the site cache, preserving it for all users to see.

* inc/
------
This Folder contains various modules built for the site


* helpers.php: Config loader & Template render function
-------------
- "tci_config($file,$key)" function for loading config files from the child theme "config/" folder
    - loads the file by name "config/(filename).json" in the child theme
    - use the $key field to load a specific value only
- tciRender( $slug, $vars=[], $echo = true ) 
    - Renders a template identified by "$slug", using the "locate_template()" funciton.
      This is intended to improve the functionality, as it allows for a list of $vars to be passed to the template,
      And also $echo = false allows for storing the template in a variable instead of outputting it

* contact.php: Contact form REST endpoint
-------------
- This defines a rest api endpoint of "tci/v1/contact" 
  which sends an email & allows for a subscribe to newsletter checkbox.

  This is used for the trucks form mentioned below.


* shortcodes.php:  Contact Information Shortcode
-----------------
This defines the shortcode [truck_contact_info], which displays the
contact information shown on the sidebar of the trucks.

There are no definable options for this shortcode.


* waire/:  Waire Calculator 
------------------
This defines the [waire] shortcode used to show the Waire calculator displayed on the site.
The file "waire/template.php" defines the template.

* waf-json/ & waf-templates/
----------------------------
"waf-json" contains json files defining forms.  Use "the_form( 'filename' )" to render the form, with no extension on the filename.
eg. to render the file "waf-json/contact.json" use "the_form( 'contact' )".
Use the "get_form( $slug )" function to store the form in a variable instead of outputtting it.

the "waf-templates" file is for creating input templates.
Filename is simply the input name + .html, eg. "input.html", "email.html", etc.
corresponding to the input "type" defined in the form json.