# MaggsWeb ShortUrl API

#### Uses Lumen v8

A back-end URL Shortener API in Lumen 8

<hr>

Each user has a unique API Key

```apacheconf
$api_key = '<unique-token>';
```

Create a new ShortURL

```php
$api_url = '../create'; 

$payload = json_encode([
    'long_url' => $_REQUEST['short_url']
]);
```

View Short Url redirect history (optional filter by Short URL)

```php
$api_url = '../link'; 

$payload = json_encode([
    'long_url' => $_REQUEST['short_url']    // Optional filter
]);
```

View User history  (optional filter by Short URL)

```php
$api_url = '../user'; 

$payload = json_encode([
    'long_url' => $_REQUEST['short_url']    // Optional filter
]);
```




<hr>

## Using Guzzle

```php
use GuzzleHttp\Client;

$client = new Client([
    'headers' => [
        'token' => $api_key
    ]
]);
$response = $client->post($api_url, [
    'body' => $payload
    ]
);
$data = $response->getBody()->getContents();
```


## Using cURL

```php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["token:$api_key"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

$data = curl_exec($ch);
curl_close($ch);
```

