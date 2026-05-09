---
title: Start Thread in Forum or Media Channel
category: Channel
order: 22
---

# `startThreadInForumOrMediaChannel`

```php
$client->channel->startThreadInForumOrMediaChannel($parameters);
```

## Description

Creates a new thread in a forum or media channel.

## Parameters

Name | Type | Required | Default
--- | --- | --- | ---
channel.id | snowflake | true | *null*
name | string | true | *null*
auto_archive_duration | integer | false | *null*
rate_limit_per_user | integer | false | *null*
message | array | true | *null*
applied_tags | array | false | *null*
flags | integer | false | *null*
reason | string | false | *null*

## Response

Returns a channel object on success.
