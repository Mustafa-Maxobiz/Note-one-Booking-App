<?php

namespace App\Services;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class WordPressUser implements ResourceOwnerInterface
{
    /**
     * Raw response
     */
    protected $response;

    /**
     * Creates new resource owner.
     */
    public function __construct(array $response = [])
    {
        $this->response = $response;
    }

    /**
     * Get user ID
     */
    public function getId()
    {
        return $this->response['ID'] ?? null;
    }

    /**
     * Get user email
     */
    public function getEmail()
    {
        return $this->response['user_email'] ?? null;
    }

    /**
     * Get user login name
     */
    public function getLogin()
    {
        return $this->response['user_login'] ?? null;
    }

    /**
     * Get user display name
     */
    public function getDisplayName()
    {
        return $this->response['display_name'] ?? null;
    }

    /**
     * Get user first name
     */
    public function getFirstName()
    {
        return $this->response['first_name'] ?? null;
    }

    /**
     * Get user last name
     */
    public function getLastName()
    {
        return $this->response['last_name'] ?? null;
    }

    /**
     * Get user nickname
     */
    public function getNickname()
    {
        return $this->response['nickname'] ?? null;
    }

    /**
     * Get user avatar URL
     */
    public function getAvatarUrl()
    {
        return $this->response['avatar_url'] ?? null;
    }

    /**
     * Get user roles
     */
    public function getRoles()
    {
        return $this->response['roles'] ?? [];
    }

    /**
     * Get user capabilities
     */
    public function getCapabilities()
    {
        return $this->response['capabilities'] ?? [];
    }

    /**
     * Get user meta data
     */
    public function getMeta($key = null)
    {
        if ($key === null) {
            return $this->response['meta'] ?? [];
        }
        
        return $this->response['meta'][$key] ?? null;
    }

    /**
     * Get user URL
     */
    public function getUrl()
    {
        return $this->response['user_url'] ?? null;
    }

    /**
     * Get user description
     */
    public function getDescription()
    {
        return $this->response['description'] ?? null;
    }

    /**
     * Get user registration date
     */
    public function getRegistered()
    {
        return $this->response['user_registered'] ?? null;
    }

    /**
     * Get user status
     */
    public function getStatus()
    {
        return $this->response['user_status'] ?? null;
    }

    /**
     * Get all user data
     */
    public function toArray()
    {
        return $this->response;
    }
}
