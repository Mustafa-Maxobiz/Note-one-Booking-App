<?php

return array (
  'default' => 'smtp',
  'mailers' => 
  array (
    'smtp' => 
    array (
      'transport' => 'smtp',
      'host' => 'smtp.gmail.com',
      'port' => 587,
      'username' => 'ghulam.murtaza@appssensation.com',
      'password' => 'tanoqmvqysgygqlv',
      'encryption' => 'tls',
      'timeout' => 60,
      'local_domain' => NULL,
      'verify_peer' => false,
      'verify_peer_name' => false,
      'stream' => [
        'ssl' => [
          'verify_peer' => false,
          'verify_peer_name' => false,
          'allow_self_signed' => true,
        ],
      ],
      'auth_mode' => null,
    ),
    'gmail' => 
    array (
      'transport' => 'smtp',
      'host' => 'smtp.gmail.com',
      'port' => 587,
      'username' => 'ghulam.murtaza@appssensation.com',
      'password' => 'tanoqmvqysgygqlv',
      'encryption' => 'tls',
      'timeout' => 60,
      'verify_peer' => false,
      'verify_peer_name' => false,
      'stream' => [
        'ssl' => [
          'verify_peer' => false,
          'verify_peer_name' => false,
          'allow_self_signed' => true,
        ],
      ],
    ),
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
    'ses' => 
    array (
      'transport' => 'ses',
    ),
    'postmark' => 
    array (
      'transport' => 'postmark',
    ),
    'resend' => 
    array (
      'transport' => 'resend',
    ),
    'sendmail' => 
    array (
      'transport' => 'sendmail',
      'path' => '/usr/sbin/sendmail -bs -i',
    ),
    'log' => 
    array (
      'transport' => 'log',
      'channel' => 'mail',
    ),
    'array' => 
    array (
      'transport' => 'array',
    ),
    'failover' => 
    array (
      'transport' => 'failover',
      'mailers' => 
      array (
        0 => 'gmail',
        1 => 'gmail_ssl',
        2 => 'log',
      ),
      'retry_after' => 60,
    ),
    'roundrobin' => 
    array (
      'transport' => 'roundrobin',
      'mailers' => 
      array (
        0 => 'gmail',
        1 => 'gmail_ssl',
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
