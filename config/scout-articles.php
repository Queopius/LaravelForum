<?php


return [

    /*
    |--------------------------------------------------------------------------
    | Searchable Attributes
    |--------------------------------------------------------------------------
    |
    | Limits the scope of a search to the attributes listed in this setting. Defining
    | specific attributes as searchable is critical for relevance because it gives
    | you direct control over what information the search engine should look at.
    |
    | Supported: Null, Array
    | Example: ["name", "email", "unordered(city)"]
    |
    */

    'searchableAttributes' => ['subject', 'body', 'slug', 'author_name', 'author_email'],

    /*
    |--------------------------------------------------------------------------
    | Custom Ranking
    |--------------------------------------------------------------------------
    |
    | Custom Ranking is about leveraging business metrics to rank search
    | results - it's crucial for any successful search experience. Make sure that
    | only "numeric" attributes are used, such as the number of sales or views.
    |
    | Supported: Null, Array
    | Examples: ['desc(comments_count)', 'desc(views_count)']
    |
    */

    'customRanking' => ['asc(sales_count)', 'desc(views_count)', 'desc(created_at)'],

    // ...
];
