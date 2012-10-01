# Products
Contributors: jazzs3quence  
Donate link:https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=AWM2TG  
Tags: e-commerce, custom post types, products  
Requires at least: 2.9  
Tested up to: 3.4.1  
Stable tag: 0.8.2  

A simple shop plugin based on custom post types with integrated support for Cart66

## Description

This plugin was originally designed to create fully-featured product pages for Cart66 sites since Cart66 does not build pages for products natively.  It allows the user to create custom product page layouts and includes a number of add-ins and widgets.

I originally developed this plugin with Cart66 in mind.  Cart66 is an awesome shop plugin for WordPress, but it lacks some of the robust features of some of those other shopping cart plugins and themes, like a dedicated storefront. Cart66 takes the stance of "we'll set you up with the system, but you're on your own with how you want to run that on your site." Which is fine.  This plugin is intended to step in the middle and create a dedicated product area for the stuff you're selling with Cart66.  If you're not using Cart66, this plugin gives you easy access to sell your products via PayPal and Google Wallet, with some additional functionality -- like cross-sales and testimonials -- that don't exist in some other e-commerce platforms.

## Other notes

It might bear reiterating that any time a plugin tries to make assumptions about the formatting and layout of your theme, there's only a very small possibility the plugin will get it right. Therefore, some theme customization will most likely be required no matter what you're using, just to make the divs and headings match up. By using this plugin, you're acknowledging that this is your responsibility as the user of the plugin and if you have any questions, I will do my best to help.

## Installation

Extract the zip file and just drop the contents in the `wp-content/plugins/` directory of your WordPress installation and then activate the Plugin from Plugins page.

## Changelog

### 0.8.2

* removed some debug code
* hid the price label if cart66 is the merchant being used
* removed conditional check for cart66 around item number

### 0.8.1

* added css classes to testimonials for styling
* replaced textarea boxes with wp_editor boxes for shipping and more info and moved them to product details metabox

### 0.8

* added ejunkie merchant option
* moved all add_meta_box calls into one function to simplify the code
* split product testimonials into its' own meta box

### 0.7

* updted note and description
* added product info meta
* moved cross-sales section down the page
* added product meta widget
* added cross-sales widget
* created options array for inquire/sold out option
* changed cross-sales meta to store id of the cross-sales item rather than the permalink (now getting data from that post id for title & permalink)
* fixed undefined variable issues
* fixed if statement that wasn't allowing cross-sales items to display on products with no tags
* added customizable thumbnail size option for related products widget
* added inquire for price link option
* added widget options to define labels for product meta
* fixed if statements
* removed prefix from add_to_cart css class
* added a css class around the payment button
* changed sanitization
* added css class to sold out h3 tag
* moved before/after widget output to inside the if statement for related products
* added member (logged in user) functionality
* added shipping info meta field
* fixed an unclosed quote
* changed "Google Checkout" to "Google Wallet"


### 0.5.1
* added related products widget
* updated testimonials widget
* added option to display testimonials widget on all pages or just sales pages
* moved widgets and post type functions into separate files
* added function to update post meta data for cpts
* moved testimonial meta box (for testimonial post type) to sidebar
* dealt with figuring out if testimonial author data is incomplete

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