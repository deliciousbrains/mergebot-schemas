## Anatomy of a Schema

1. [Primary Keys](#primary-keys)
1. [Foreign Keys](#foreign-keys)
1. [Meta Tables](#meta-tables)
1. [Key/Value Relationships](#keyvalue-relationships)
1. [Shortcodes](#shortcodes)
1. [Content](#content)
1. [Ignored Queries](#ignored-queries)
1. [Table Prefix in Keys](#table-prefixes-in-keys)

Schemas are a piece of JSON used by Mergebot to describe the database relationships of a part of a site (e.g. WordPress core, a plugin etc.). Schemas include information on primary/foreign keys, relationships of IDs in key/value pair tables, content replacements and shortcodes. For example, if your plugin uses custom tables, inserts data into the options table, inserts IDs into the post content, or implements custom shortcodes you can create a custom schema to tell Mergebot how to handle this data.

We currently support these [plugins](https://github.com/deliciousbrains/mergebot-schemas/tree/master/plugins), but you can add custom schemas you have written by going to the [team settings page](/settings/teams/[team_id]#/schemas) and navigating to the "Schemas" tab.

### Primary Keys

If you have custom tables, primary keys can be defined in a schema using the following format:

```
{
    "primaryKeys": {
        "{table name without prefix}": {
            "key": [
                "{primary key column}"
            ]
        }
    }
}
```

```
{
    "primaryKeys": {
        "orders": {
            "key": [
                "order_id"
            ]
        }
    }
}
```

Mergebot assumes a single key is an AUTO INCREMENT column. You can turn this off using the `auto_increment` attribute:

```
{
    "primaryKeys": {
        "log": {
            "key": [
                "name"
            ],
            "auto_increment": false
        }
    }
}
```

Compound primary keys are also supported:

```
{
    "primaryKeys": {
        "{table name without prefix}": {
            "key": [
                "{compound key column 1}",
                "{compound key column 2}"
             ],
             "auto_increment": false
         },
    }
}
```

```
{
    "primaryKeys": {
        "term_relationships": {
            "key": [
                "object_id",
                "term_taxonomy_id"
             ],
             "auto_increment": false
         },
    }
}
```

### Foreign Keys

If you have custom tables, foreign keys can be defined in a schema using the following format:

```
{
    "foreignKeys": {
        "{source table name without prefix}:{source column}": "{destination table name without prefix}:{destination column}"
    }
}
```

```
{
    "foreignKeys": {
        "orders:user_id": "users:ID"
    }
}
```

### Meta Tables

Tables that contain key/value data (e.g. `options`, `postmeta`) need to have their 'key' column defined so Mergebot can use it for conflict processing. If the table has a foreign key reference to another table, like `post_id`, this needs to be included in the key definition. Custom meta tables can be defined in the following way:

```
{
    "metaTables": {
        "{table name without prefix}": {
            "keys": [
                "{key column 1}",
                "{key column 2}"
             ],
         },
    }
}
```

```
{
    "metaTables": {
        "woocommerce_payment_tokenmeta": {
            "keys": [
                "payment_token_id",
                "meta_key"
             ],
         },
    }
}
```

If a meta table has only one key column, no reference to another table, and enforces uniqueness based on that column (like the WordPress `options` table), the table can be defined like:

```
{
    "metaTables": {
        "{table name without prefix}": {
            "keys": [
                "{key column}"
             ],
             "unique": true
         },
    }
}
```

```
{
    "metaTables": {
        "settings": {
            "keys": [
                "settings_key"
             ],
             "unique": true
         },
    }
}
```

### Key/Value Relationships

Sometimes an ID is stored in the value of a row in a key/value table (e.g. `options`, `postmeta` etc.). Mergebot needs to know what table and column this ID maps to so it can handle it properly. They can be defined using the following format:

```
{
    "relationships": {
        "{table name without prefix}": [
            {
                "{key column}": "{key value}",
                "{value column}": "{table name of primary key without prefix}",
            }
        ]
    }
}
```

Serialized data is also supported:

```
{
    "relationships": {
        "{table name without prefix}": [
            {
                "{key column}": "{key value}",
                "{value column}": "{table name of primary key without prefix}",
                "serialized": {
                    "key": "ignore",
                    "val": "{table name without prefix}:{column}"
                }
            }
        ]
    }
}
```

Note that key values can also use regex for wildcard searching. When mapping serialized data you can use `ignore` to specify an ignored key or value.

```
{
    "relationships": {
        "options": {
            "(.*)category_children": {
                "option_name": "(.*)category_children",
                "option_value": "terms",
                "serialized": {
                    "key": "terms:term_id",
                    "val": {
                        "key": "ignore",
                        "val": "terms:term_id"
                    }
                }
            }
        }
    }
}
```

We also support keys that have IDs embedded within the string, using a `{table:column}` notation. For example:

```
{
    "relationships": {
        "options": {
            "_{posts:ID}_custom_data": {
                "option_name": "_{posts:ID}_custom_data",
                "option_value": "ignore",
            }
        }
    }
}
```

### Shortcodes

Shortcodes often contain IDs and so we need to tell Mergebot what table/column those IDs relate to. They can be defined using the following format:

```
{
    "shortcodes": {
        "{shortcode name}": {
            "parameters": [
                "{param1}",
                "{param2}"
            ]
        }
    }
}
```

Although Mergebot already handles this out of the box, a good real-world example of this is WordPress core's *gallery* shortcode. If Mergebot didn't already know about this, we could define the following schema:

```
{
    "shortcodes": {
        "gallery": {
            "parameters": [
                "id",
                "ids",
                "include",
                "exclude",
            ]
        }
    }
}
```

By default the parameters assume that you are using a post ID, but you can change this by specifying a different table:

```
{
    "shortcodes": {
        "{shortcode name}": {
            "parameters": [
                {
                    "name": "{param1}",
                    "table": "{table name without prefix}"
                },
                {
                    "name": "{param2}",
                    "table": "{table name without prefix}"
                },
            ]
        }
    }
}
```
Again, Mergebot already knows the *gallery* shortcode, but it is a good example:

```
{
    "shortcodes": {
        "gallery": {
            "parameters": [
                {
                    "name": "id",
                    "table": "posts"
                },
                {
                    "name": "ids",
                    "table": "posts"
                },
                {
                    "name": "include",
                    "table": "posts"
                },
                {
                    "name": "exclude",
                    "table": "posts"
                },
            ]
        }
    }
}
```

By default Mergebot will look for shortcodes in the following locations:

* `posts` > `post_content`
* `posts` > `post_excerpt`
* `postmeta` > `meta_value`

However you can add custom search locations by specifying them in a `search` object:

```
{
    "shortcodes": {
        "{shortcode name}": {
            "parameters": [
                "{parameter}"
            ],
            "search": [
                {
                    "table": "{table name without prefix}",
                    "column": "{column}",
                }
            ]
        }
    }
}
```

```
{
    "shortcodes": {
        "gallery": {
            "parameters": [
                "id"
            ],
            "search": [
                {
                    "table": "products",
                    "column": "description",
                }
            ]
        }
    }
}
```

### Content


Sometimes IDs can be found in post content and must be handled by Mergebot. The *content* schema allows you to define what table/column to search and what to search for. Mergebot assumes the ID found relates to the primary key of the table searched.


```
{
    "content": {
        "{table name without prefix}:{column}": "{regex}"
    }
}
```

Although Mergebot already handles this out of the box, a good real-world example of this is WordPress core adding IDs in the class attributes of images. If Mergebot didn't already know about this, we could define the following schema:

```
{
    "content": {
        "posts:post_content": "(?<=wp-image-)\\d+"
    }
}
```

### Ignored Queries

Sometimes plugins and themes run queries on the database that are specific to the environment and don’t need to be included in a changeset to be applied later to production. We can describe these types of queries in the ‘ignore’ section of the schema. E.g.

```
{
    "ignore": [
        {
            "{table name without prefix}:{column}": "{regex}"
        },
    ]
}
```

```
{
    "ignore": [
        {
            "options:option_name": "_transient_(.*)"
        },
    ]
}
```

If you want to ignore queries based on just the table name you can simply exclude the `:{column}` section of the key. For example:

```
{
    "ignore": [
        {
            "table_name": "(.*)"
        },
    ]
}
```

### Table Prefixes in Keys

There are times when data is stored which includes the database’s table prefix at the start of data keys. Although this mainly happens in WordPress core, the schema allows you to describe these instances:

```
{
    "tablePrefixes": [
        {
            "{table name without prefix}:{column}": "{key without prefix}"
        },
    ]
}
```

```
{
    "tablePrefixes": [
        {
            "options:option_name": "user_roles"
        },
    ]
}
```
