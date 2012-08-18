# Products
Contributors: jazzs3quence  
Donate link:https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=AWM2TG  
Tags: e-commerce, custom post types, products  
Requires at least: 2.9  
Tested up to: 3.4.1  
Stable tag: 0.5  

A simple shop plugin based on custom post types with integrated support for Cart66

## Description

This plugin was originally designed to create fully-featured product pages for Cart66 sites since Cart66 does not build pages for products natively.  It allows the user to create custom product page layouts and includes a number of add-ins and widgets.

###Author's note

Before you start yelling about how this plugin is barely functional or requires too much configuration (which, hopefully, when it's finished, it won't, really), the reason I developed this plugin was primarily with Cart66 in mind.  Cart66 is an awesome shop plugin for WordPress, but it lacks some of the robust features of some of those other shopping cart plugins and themes, like a dedicated storefront. Cart66 takes the stance of "we'll set you up with the system, but you're on your own with how you want to run that on your site." Which is fine.  This plugin is intended to step in the middle and create a dedicated product area for the stuff you're selling with Cart66.  And if you're not using Cart66, this plugin will (hopefully) support PayPal and Google Checkout, as well.

It might bear reiterating that any time a plugin tries to make assumptions about the formatting and layout of your theme, there's only a very small possibility the plugin will get it right. Therefore, some theme customization will most likely be required no matter what you're using, just to make the divs and headings match up. By using this plugin, you\'re acknowledging that this is your responsibility as the user of the plugin and if you have any questions, I will do my best to help.

## Installation

Extract the zip file and just drop the contents in the `wp-content/plugins/` directory of your WordPress installation and then activate the Plugin from Plugins page.

## Changelog

### 0.5

* switched to @pdclark's f/gitweb fork of GitHub Updater class
* removed ~Current Version:x~ line in readme
* added git uri to plugin header
* fixed some bugs in new updater class for plugin details display
* changed Google Checkout to Google Wallet
* added testimonials post type if shop testimonials are active
* added textdomain for l10n
* added shop/product testimonial defaults
* added icons for testomonials post type
* added (renamed) quotes/testimonials widget from opal-ecommerce theme

### 0.4

* added cart66 product selector
* added WordPress GitHub Plugin Updater by jkudish

### 0.3.1

* added defaults array
* removed old add to cart button option in the edit product page
* set custom add to cart button as the default for cart66
* removed old image uploader files
* added admin scripts
* consolodated custom icons into one function
* added presstrends
* removed old uploader script and replaced with WordPress media uploader
* added add-to-cart button option
* adds html input area for button code
* set the html/url option to not display if cart66 is selected as merchant
* loops through products & grabs permalink to store as meta value on the product for cross-sales
* added display cross-sales option
* added generic true/false option array
* implemented merchant options on add product page
* added display option on options page for HTML/URL buttons for Google/PayPal
* added new option for hTML embed code or URL to checkout page

### 0.3

* added settings page menu item
* fixed menu icon issue
* added readme.md
* pulled out notes and added phpdoc-style comments
* added options page
* added `option-setup.php` -- this is where the markup for all the options will live
* added global definition for plugin dir
* added `option-setup.php call
* added settings call and runs the `_do_options` function to load the options from `option-setup.php`
* added merchant options array
* added merchant options setting

### 0.2

* changed tag icon to match the small icon and adjusted credit accordingly
* added cross sales text meta field to use as anchor text for the cross sales link
* added post thumbnail support to not assume the theme enables it (thumbnail size will need to be set within the theme, see the note on line 76)

### 0.1

* Initial development
* added author's note and plugin meta
* created the post type
* added a custom taxonomy
* created meta boxes for button codes
* added custom icons
* defined global variables for plugin path and plugin image path