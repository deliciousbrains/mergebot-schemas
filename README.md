# Mergebot Schemas

Schemas are a piece of JSON used by Mergebot to describe the database relationships of a part of a site (e.g. WordPress core, a plugin etc.). 

Schemas include information on primary/foreign keys, relationships of IDs in key/value pair tables, content replacements, shortcodes, and other data. 

For example, if your plugin uses custom tables, inserts data into the options table, inserts IDs into the post content, or implements custom shortcodes you can create a custom schema to tell Mergebot how to handle this data.

## Global Schemas

All the schemas in this repository are used by the Mergebot application by default. This means all the plugins that have schemas defined [here](https://github.com/deliciousbrains/mergebot-schemas/tree/master/plugins) are compatible with Mergebot.

## Custom Schemas

If you require another plugin to be compatible with Mergebot, you can create your own custom schema for a plugin or theme and upload it to Mergebot. [Learn more](https://github.com/deliciousbrains/mergebot-schemas/tree/master/ANATOMY.md).

## Plugin Developers

