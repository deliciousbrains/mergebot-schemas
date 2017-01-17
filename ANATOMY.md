## Anatomy of a Schema

1. [Primary Keys](#primary-keys)
1. [Foreign Keys](#foreign-keys)
1. [Key/Value Relationships](#keyvalue-relationships)
1. [Shortcodes](#shortcodes)
1. [Content](#content)
1. [Ignored Queries](#ignored-queries)
1. [Table Prefix in Keys](#table-prefixes-in-keys)

Schemas are a piece of JSON used by Mergebot to describe the database relationships of a part of a site (e.g. WordPress core, a plugin etc.). Schemas include information on primary/foreign keys, relationships of IDs in key/value pair tables, content replacements and shortcodes. For example, if your plugin uses custom tables, inserts data into the options table, inserts IDs into the post content, or implements custom shortcodes you can create a custom schema to tell Mergebot how to handle this data.

You can define schemas from the [Schemas page](/settings/teams/[team_id]#/schemas) in your team settings.

### Primary Keys

If you have custom tables, primary keys can be defined in a schema using the following format:

```
{
    "primaryKeys": {
        "{table name without prefix}": "{primary key column}"
    }
}
```

```
{
    "primaryKeys": {
        "orders": "order_id"
    }
}
```

Compound primary keys are also supported:

```
{
    "primaryKeys": {
        "{table name without prefix}": [
            "{compound key column 1}",
            "{compound key column 2}"
        ]
    }
}
```

```
{
    "primaryKeys": {
        "term_relationships": [
            "object_id",
            "term_taxonomy_id"
        ]
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
In the following example, we're defining a relationship between certain rows in the `postmeta` table to rows in the `users` table. This happens to be relationship in WooCommerce.

```
{
    "relationships": {
        "postmeta": [
            {
                "meta_key": "_customer_user",
                "meta_value": "users",
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

Note that key values can also use `%` as a wildcard search token. When mapping serialized data you can use `ignore` to specify an ignored key or value.

```
{
    "relationships": {
        "options": [
            {
                "option_name": "%category_children",
                "option_value": "terms",
                "serialized": {
                    "key": "terms:term_id",
                    "val": {
                        "key": "ignore",
                        "val": "terms:term_id"
                    }
                }
            }
        ]
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
