# Mergebot Schemas

Schemas are a piece of JSON used by Mergebot to describe the database relationships of a part of a site (e.g. WordPress core, a plugin etc.). 

Schemas include information on primary/foreign keys, relationships of IDs in key/value pair tables, content replacements, shortcodes, and other data. 

For example, if your plugin uses custom tables, inserts data into the options table, inserts IDs into the post content, or implements custom shortcodes you can create a custom schema to tell Mergebot how to handle this data.