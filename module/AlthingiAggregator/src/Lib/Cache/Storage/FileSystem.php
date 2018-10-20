<?php
namespace AlthingiAggregator\Lib\Cache\Storage;

use Traversable;
use Zend\Cache\Storage\Adapter;
use Zend\Cache\Storage\Capabilities;
use Zend\Cache\Storage\StorageInterface;

class FileSystem implements StorageInterface
{
    private $options = [
        'cache_dir' => './'
    ];

    /**
     * Set options.
     *
     * @param array|Traversable|Adapter\AdapterOptions $options
     * @return StorageInterface Fluent interface
     */
    public function setOptions($options)
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    /**
     * Get options
     *
     * @return Adapter\AdapterOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get an item.
     *
     * @param  string $key
     * @param  bool $success
     * @param  mixed $casToken
     * @return mixed Data on success, null on failure
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function getItem($key, & $success = null, & $casToken = null)
    {
        // TODO: Implement getItem() method.
    }

    /**
     * Get multiple items.
     *
     * @param  array $keys
     * @return array Associative array of keys and values
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function getItems(array $keys)
    {
        // TODO: Implement getItems() method.
    }

    /**
     * Test if an item exists.
     *
     * @param  string $key
     * @return bool
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function hasItem($key)
    {
        // TODO: Implement hasItem() method.
    }

    /**
     * Test multiple items.
     *
     * @param  array $keys
     * @return array Array of found keys
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function hasItems(array $keys)
    {
        // TODO: Implement hasItems() method.
    }

    /**
     * Get metadata of an item.
     *
     * @param  string $key
     * @return array|bool Metadata on success, false on failure
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function getMetadata($key)
    {
        // TODO: Implement getMetadata() method.
    }

    /**
     * Get multiple metadata
     *
     * @param  array $keys
     * @return array Associative array of keys and metadata
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function getMetadatas(array $keys)
    {
        // TODO: Implement getMetadatas() method.
    }

    /**
     * Store an item.
     *
     * @param  string $key
     * @param  mixed $value
     * @return bool
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function setItem($key, $value)
    {
        if (is_file($key)) {
            return false;
        }

        $pathArray = explode('/', $key);
        $fileName = array_pop($pathArray);
        $path = sprintf('%s/%s/%s', $this->options['cache_dir'], implode('/', $pathArray), $fileName);

        @mkdir(implode('/', $pathArray), 0777, true);
        return file_put_contents($path, $value) !== false;
    }

    /**
     * Store multiple items.
     *
     * @param  array $keyValuePairs
     * @return array Array of not stored keys
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function setItems(array $keyValuePairs)
    {
        // TODO: Implement setItems() method.
    }

    /**
     * Add an item.
     *
     * @param  string $key
     * @param  mixed $value
     * @return bool
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function addItem($key, $value)
    {
        // TODO: Implement addItem() method.
    }

    /**
     * Add multiple items.
     *
     * @param  array $keyValuePairs
     * @return array Array of not stored keys
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function addItems(array $keyValuePairs)
    {
        // TODO: Implement addItems() method.
    }

    /**
     * Replace an existing item.
     *
     * @param  string $key
     * @param  mixed $value
     * @return bool
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function replaceItem($key, $value)
    {
        // TODO: Implement replaceItem() method.
    }

    /**
     * Replace multiple existing items.
     *
     * @param  array $keyValuePairs
     * @return array Array of not stored keys
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function replaceItems(array $keyValuePairs)
    {
        // TODO: Implement replaceItems() method.
    }

    /**
     * Set an item only if token matches
     *
     * It uses the token received from getItem() to check if the item has
     * changed before overwriting it.
     *
     * @param  mixed $token
     * @param  string $key
     * @param  mixed $value
     * @return bool
     * @throws \Zend\Cache\Exception\ExceptionInterface
     * @see    getItem()
     * @see    setItem()
     */
    public function checkAndSetItem($token, $key, $value)
    {
        // TODO: Implement checkAndSetItem() method.
    }

    /**
     * Reset lifetime of an item
     *
     * @param  string $key
     * @return bool
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function touchItem($key)
    {
        // TODO: Implement touchItem() method.
    }

    /**
     * Reset lifetime of multiple items.
     *
     * @param  array $keys
     * @return array Array of not updated keys
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function touchItems(array $keys)
    {
        // TODO: Implement touchItems() method.
    }

    /**
     * Remove an item.
     *
     * @param  string $key
     * @return bool
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function removeItem($key)
    {
        // TODO: Implement removeItem() method.
    }

    /**
     * Remove multiple items.
     *
     * @param  array $keys
     * @return array Array of not removed keys
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function removeItems(array $keys)
    {
        // TODO: Implement removeItems() method.
    }

    /**
     * Increment an item.
     *
     * @param  string $key
     * @param  int $value
     * @return int|bool The new value on success, false on failure
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function incrementItem($key, $value)
    {
        // TODO: Implement incrementItem() method.
    }

    /**
     * Increment multiple items.
     *
     * @param  array $keyValuePairs
     * @return array Associative array of keys and new values
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function incrementItems(array $keyValuePairs)
    {
        // TODO: Implement incrementItems() method.
    }

    /**
     * Decrement an item.
     *
     * @param  string $key
     * @param  int $value
     * @return int|bool The new value on success, false on failure
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function decrementItem($key, $value)
    {
        // TODO: Implement decrementItem() method.
    }

    /**
     * Decrement multiple items.
     *
     * @param  array $keyValuePairs
     * @return array Associative array of keys and new values
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function decrementItems(array $keyValuePairs)
    {
        // TODO: Implement decrementItems() method.
    }

    /**
     * Capabilities of this storage
     *
     * @return Capabilities
     */
    public function getCapabilities()
    {
        // TODO: Implement getCapabilities() method.
    }
}
