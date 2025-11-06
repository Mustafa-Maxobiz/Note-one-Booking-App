<?php

return array (
  'default' => 'smtp',
  'mailers' => 
  array (
    // Primary configuration for online server
    'smtp' => 
    array (
      'transport' => 'smtp',
      'host' => 'smtp.gmail.com',
      'port' => 465,  // Try SSL port 465 instead of 587
      'username' => 'ghulam.murtaza@appssensation.com',
      'password' => 'tanoqmvqysgygqlv',
      'encryption' => 'ssl',  // Use SSL instead of TLS
      'timeout' => 60,
      'local_domain' => NULL,
      'verify_peer' => false,
      'verify_peer_name' => false,
    ),
    
    // Alternative Gmail configuration
    'gmail_ssl' => 
    array (
      'transport' => 'smtp',
      'host' => 'smtp.gmail.com',
      'port' => 465,
      'username' => 'ghulam.murtaza@appssensation.com',
      'password' => 'tanoqmvqysgygqlv',
      'encryption' => 'ssl',
      'timeout' => 60,
      'verify_peer' => false,
      'verify_peer_name' => false,
    ),
    
    // Alternative host configuration
    'gmail_alt' => 
    array (
      'transport' => 'smtp',
      'host' => 'smtp.googlemail.com',
      'port' => 465,
      'username' => 'ghulam.murtaza@appssensation.com',
      'password' => 'tanoqmvqysgygqlv',
      'encryption' => 'ssl',
      'timeout' => 60,
      'verify_peer' => false,
      'verify_peer_name' => false,
    ),
    
    // Standard port 25 (often allowed on servers)
    'gmail_port25' => 
    array (
      'transport' => 'smtp',
      'host' => 'smtp.gmail.com',
      'port' => 25,
      'username' => 'ghulam.murtaza@appssensation.com',
      'password' => 'tanoqmvqysgygqlv',
      'encryption' => 'tls',
      'timeout' => 60,
      'verify_peer' => false,
      'verify_peer_name' => false,
    ),
    
    // Fallback to log driver if SMTP fails
    'log' => 
    array (
      'transport' => 'log',
      'channel' => 'mail',
    ),
    
    // Array driver for testing
    'array' => 
    array (
      'transport' => 'array',
    ),
    
    // Failover configuration
    'failover' => 
    array (
      'transport' => 'failover',
      'mailers' => 
      array (
        0 => 'gmail_ssl',
        1 => 'gmail_alt',
        2 => 'gmail_port25',
        3 => 'log',
      ),
      'retry_after' => 60,
    ),
  ),
  'from' => 
  array (
    'address' => 'ghulam.murtaza@appssensation.com',
    'name' => 'Online Lesson Booking System',
  ),
);
