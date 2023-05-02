<?php

$url = "http://horlix.com/wp-json/wp/v2/pages";

  if(strpos($url, '/wp/v2/pages') !== false) 
  {
      // Get the content from the $url
      $data = file_get_contents($url);

      // Call the XmlGenerator function here
      XmlGenerator($data);
  }
  elseif(strpos($url, '/wp/v2/posts') !== false) 
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

      // Call the XmlGenerator function here
      XmlGenerator($data);
  }
  else 
  {
    echo "URL is invalid";
    
  }

  // Function to create XML
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
          
      }

      // Show XML in the browser
      header('Content-type: text/xml');
      echo $xml->asXML();
    }

?>
