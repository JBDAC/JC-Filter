A simple Wordpress plugin. Filter posts and pages based on the jc_filter custom field and display them in a custom list. Display an empty page when nothing matches the jc_filter. 

Usage:
Add a custom field called to each required post called jc_filter and set its value to a value like "includeA"

In a page, insert a shortcode like this:
[jc_filter include="includeA, includeB" exclude="excludeMe" date_format="Y-m-d" date_position="start" order="desc"]

This will create a list of posts drawn from posts which have a custom field called jc_filter with a relevant value. Use PHP date format characters. 
So that posts are also hidden from the default 'uncategorised' default category, we add a couple of additional filters for this when not in admin mode.

Installation:
In your Wordpress plugins directory: wp-content/plugins/ add a subdirectory called jc-filter
Copy the jc_filter.php file to wp-content/plugins/jc-filter/

Activate the plugin within Wordpress.
