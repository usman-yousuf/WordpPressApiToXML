<?php
// Replace the {post_id} with the ID of the post you want to retrieve metadata for
$post_id = 16427;

// Build the API URL
$url = "https://horlix.com/wp-json/wp/v2/posts/{$post_id}?_fields=meta";
var_dump($url);
// Fetch the data using cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Parse the response data as a JSON object
$data = json_decode($response);

// Build an XML file with the post metadata
$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><post></post>');
foreach ($data->meta as $meta) {
    $xml->addChild($meta->key, $meta->value->rendered);
}
// Output the XML document
header('Content-type: text/xml');
echo $xml->asXML();
