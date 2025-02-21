# MaggsWeb ShortUrl API

[![StyleCI](https://github.styleci.io/repos/319776559/shield?branch=master)](https://github.styleci.io/repos/319776559?branch=master)

A back-end URL Shortener API
- Built with Lumen v8
- Uses GuzzleHttp\Client
- Uses Basic Auth

<hr>

```php
$client = new Client([
    'headers' => [
        'Authorization' => 'Basic ...',
        'Content-Type' => 'application/json'
    ]
]);
```

### Create a new ShortURL from a URL
#### with optional suggested short url

```php
$response = $client->post($host.'/create', [
    'body' => json_encode([
        'long_url' => $long_url,
        'short_url' => $suggested_short_url // optional
    ])
]);
return $response->getBody()->getContents();
```

### View ShortUrl redirect history

```php
$response = $client->post($host.'/link', [
    'body' => json_encode([
        'short_url' => $short_url
    ])
]);
return $response->getBody()->getContents();
```

### View User history
####  (optionally filtered by ShortURL)

```php
$response = $client->post($host.'/user', [
    'body' => json_encode([                 
        'short_url' => $short_url // optional filter
    ])
]);
return $response->getBody()->getContents();
```

### Delete Short Url
#### and associated activity

```php
$response = $client->delete($host.'/link', [
    'body' => json_encode([                 
        'short_url' => $_REQUEST['short_url']
    ])
]);
return $response->getBody()->getContents();
```

### Delete User Account
#### and all associated links & activity

```php
$response = $client->delete($host.'/user');
return $response->getBody()->getContents();
```
