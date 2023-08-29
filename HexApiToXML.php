<?php

$url = "http://example.com/wp-json/wp/v2/pages";

if(strpos($url, '/wp/v2/pages') !== false || strpos($url, '/wp/v2/posts') !== false) 
{
    // Get the content from the $url
    $data = file_get_contents($url);
    // Call the XmlGenerator function here
    XmlGenerator($data);
}
elseif(strpos($url, '/wp/v2/types') !== false) 
{
    // Get the content from the $url
    $data = file_get_contents($url);
      // var_dump($data);
    // Call the XmlGenerator function here
    XmlGenerator($data);
}
else 
{
    echo "This URL is invalid";
}
    
 // Function to recursively iterate over the data array
 function arrayToXml($data, &$xml) {
    foreach($data as $key => $value) {
      // If value is an array, then recursively call the function
      if(is_array($value)) {
        // If the key is numeric, use a generic tag name
        if(is_numeric($key)) {
          $key = "child";
        }
        if(!preg_match('/^[a-zA-Z_][a-zA-Z0-9_.-]*$/', $key) || strpos($key, '@') === 0) {
            $key = str_replace('@', '', $key); 
        }
        $subNode = $xml->addChild($key);
        arrayToXml($value, $subNode);
      }
      else {
        // Add the key-value pair to the XML
        if(!empty($value)) {
            if(is_string($value)) {
                // If $value is a string, escape special characters in the value
                $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
              } elseif(is_object($value)) {
                // If $value is an object, convert it to an array and recursively call the function
                $value = (array)$value;
                if(is_numeric($key)) {
                    $key = "child";
                }
                if(!preg_match('/^[a-zA-Z_][a-zA-Z0-9_.-]*$/', $key) || strpos($key, '@') === 0) {
                    $key = str_replace('@', '', $key); 
                }
                $subNode = $xml->addChild($key);
                arrayToXml($value, $subNode);
                continue;
              }
            // Check if the key is a valid XML element name
            
            if(!preg_match('/^[a-zA-Z_][a-zA-Z0-9_.-]*$/', $key) || strpos($key, '@') === 0) {
              $key = str_replace(' ', '-', $key); 
            }
            if(!preg_match('/^[a-zA-Z_][a-zA-Z0-9_.-]*$/', $key) || strpos($key, '@') === 0) {
                $key = str_replace('@', '', $key); 
            }
            // Check if the value is a valid XML character data
            if(!preg_match('//u', $value)) {
              $value = '';
            }
            if(is_numeric($key)) {
                $key = "child";
            }
            $xml->addChild($key, $value);
        } else {
            // Check if the key is a valid XML element name
            if(!preg_match('/^[a-zA-Z_][a-zA-Z0-9_.-]*$/', $key) || strpos($key, '@') === 0) {
                $key = str_replace('@', '', $key); 
            }
            
            $xml->addChild($key);
        }
      }
    }
  }
  

// Function to generate XML
function XmlGenerator($data)
{
    $response = json_decode($data);

    /**
     * If JSON decoding failed, handle the error
     * The decoded data is not an array, handle the error
     */

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Error decoding JSON: " . json_last_error_msg();
        exit;
    }

    if (!is_array($response)) {
        echo "Invalid data format";
        exit;
    }
    
    // Replace HTML entity name to number and Loop through the data and modify it for XML
     
    foreach ($response as $data) {
        if (isset($data->content->rendered)) {
            $data->content->rendered = strip_tags($data->content->rendered);
            $data->content->rendered = str_replace('&nbsp;', '&#160;', $data->content->rendered);
        }
    }

    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><root></root>');
    foreach ($response as $item) {
        $itemElement = $xml->addChild('child');
        $itemElement->addChild('id', $item->id);
        $itemElement->addChild('date', $item->date);
        $itemElement->addChild('date_gmt', $item->date_gmt);
        $itemElement->addChild('guid', $item->guid->rendered);
        $itemElement->addChild('modified', $item->modified);
        $itemElement->addChild('modified_gmt', $item->modified_gmt);
        $itemElement->addChild('slug', $item->slug);
        $itemElement->addChild('status', $item->status);
        $itemElement->addChild('type', $item->type);
        $itemElement->addChild('link', $item->link);
        $itemElement->addChild('title', $item->title->rendered);
        $itemElement->addChild('content', $item->content->rendered);
        
        // Add Yoast SEO meta data
        if (isset($item->yoast_head_json)) {
            $data = $item->yoast_head_json;
            // var_dump($data);
            $yoastMetaElement = $itemElement->addChild('yoast_head_json');
            // $data = json_decode($item->yoast_head_json, true);
            // var_dump($data);
            arrayToXml($data, $yoastMetaElement);
        }
    }

    // Show XML in the browser
    header('Content-type: text/xml');
    echo $xml->asXML();
} 



