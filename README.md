# wp-graphql-route-field

This plugin adds a `route` field to WP GraphQL to get post data via URL path.

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

## URL Rewrites

URL rewrites for custom post types are respected because this plugin gets data by path, not uri.
TeamMember is a custom post type called "team_members" which is rewritten to "team" and has a WP GraphQL singular name of TeamMember.
```graphql
route(path: "/team/tyler/") {
    ... on TeamMember {
      title
      content
    }
  }
```

Feel free to add slashes to the path at the beginning and end or leave them out. `/team/tyler/`, `/team/tyler`, `team/tyler` all work.
