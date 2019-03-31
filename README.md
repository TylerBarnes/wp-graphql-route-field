# wp-graphql-route-field

This plugin adds a `route` field to WP GraphQL to get post data via URL path.

URL rewrites for custom post types are respected because this plugin gets data by path, not uri.

Usage:

```graphql
route(path: "/about-page") {
    __typename
    
    ... on Post {
      title
      content
      post_specific_field
    }
    
    ... on Page {
      title
      content
      page_specific_field
    }
  } 
```

Returns:

```json
{
  "data": {
    "route": {
      "__typename": "Page",
      "title": "About page",
      "content": "\n<p>this is the about page</p>\n",
      "page_specific_field": "Yep, this is a page alright"
    }
  }
}
```
