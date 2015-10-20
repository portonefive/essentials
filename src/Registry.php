<?php namespace PortOneFive\Essentials;

use Cache;
use Illuminate\Foundation\Application;

class Registry
{
    protected $app;
    protected $cacheData = null;
    protected $table     = 'registry';
    protected $cache     = 'registry.cache';

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->loadRegistryIntoCache();
    }

    /**
     * Get value from registry
     *
     * @param  string $key
     * @param  string $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        list($baseKey, $searchKey) = $this->fetchKey($key);

        return $this->fetchValue($baseKey, $searchKey, $default);
    }

    public function exists($key)
    {
        return ! is_null($this->get($key));
    }

    /**
     * Get all from registry
     *
     * @return mixed
     */
    public function all()
    {
        return $this->cacheData;
    }

    /**
     * Store value into registry
     *
     * @param  string $key
     * @param  mixed  $value
     *
     * @return bool
     */
    public function set($key, $value)
    {
        list($baseKey, $searchKey) = $this->fetchKey($key);
        $existingValue = $this->get($baseKey);

        if ($baseKey != $searchKey) {
            $object = ! is_null($existingValue) && is_array($existingValue) ? $existingValue : [];
            $level  = '';
            $keys   = explode('.', $searchKey);

            foreach ($keys as $key) {
                $level .= '.' . $key;
                if (trim($level, '.') == $searchKey) {
                    array_set($object, trim($level, '.'), $value);
                } else {
                    array_set($object, trim($level, '.'), []);
                }
            }

            $value = $object;
        }

        if ( ! is_null($existingValue)) {
            $this->app['db']->table($this->table)->where('key', '=', $baseKey)->update(
                ['value' => json_encode($value)]
            );
        } else {
            $this->app['db']->table($this->table)->insert(['key' => $baseKey, 'value' => json_encode($value)]);
        }

        $this->cacheData[$baseKey] = $value;

        Cache::forever($this->cache, $this->cacheData);

        return true;
    }

    /**
     * Delete existing value from registry
     *
     * @param  string $key
     *
     * @throws \Exception
     * @return bool
     */
    public function delete($key)
    {
        list($baseKey, $searchKey) = $this->fetchKey($key);
        $existingValue = $this->get($baseKey);

        if (is_null($existingValue)) {
            throw new \Exception("Item [$key] does not exists");
        }

        if ($baseKey !== $searchKey) {
            array_forget($existingValue, $searchKey);

            if (empty($existingValue)) {
                $this->app['db']->table($this->table)->where('key', '=', $baseKey)->delete();
                unset($this->cacheData[$baseKey]);
            } else {
                $this->app['db']->table($this->table)->where('key', '=', $baseKey)->update(
                    ['value' => json_encode($existingValue)]
                );
                $this->cacheData[$baseKey] = $existingValue;
            }
        } else {
            $this->app['db']->table($this->table)->where('key', '=', $baseKey)->delete();
            unset($this->cacheData[$baseKey]);
        }

        Cache::forever($this->cache, $this->cacheData);

        return true;
    }

    /**
     * Fetch all values from a key
     *
     * @param  string $key
     * @param  string $default
     *
     * @return mixed
     */
    public function dump($key, $default = null)
    {
        list($baseKey,) = $this->fetchKey($key);

        return $this->fetchValue($baseKey, null, $default);
    }

    /**
     * Clear registry
     *
     * @return bool
     */
    public function flush()
    {
        Cache::forget($this->cache);
        $this->cacheData = null;
        $this->app['db']->table($this->table)->truncate();

        return true;
    }

    /**
     * Get registry key
     *
     * @param  string $key
     *
     * @return array
     */
    protected function fetchKey($key)
    {
        if (str_contains($key, '.')) {
            $keys   = explode('.', $key);
            $search = array_except($keys, 0);

            return [array_get($keys, 0), implode('.', $search)];
        }

        return [$key, $key];
    }

    /**
     * Get key value
     *
     * @param  string $key
     * @param  string $searchKey
     * @param null    $default
     *
     * @return mixed
     */
    protected function fetchValue($key, $searchKey = null, $default = null)
    {
        if ( ! isset($this->cacheData[$key])) {
            return $default;
        }

        $object = $this->cacheData[$key];

        if ($key == $searchKey) {
            return $object;
        }

        return ! is_null($searchKey) ? array_get($object, $searchKey, $default) : array_get($object, $key, $default);
    }

    /**
     * Load all registry entries from DB
     *
     * @return array
     */
    protected function loadRegistryIntoCache()
    {
        $db = $this->app['db'];

        $this->cacheData = Cache::rememberForever(
            $this->cache,
            function () use ($db) {
                $cache = [];
                foreach ($db->table($this->table)->get() as $registryEntry) {
                    $cache[$registryEntry->key] = json_decode($registryEntry->value, true);
                }

                return $cache;
            }
        );
    }
}
