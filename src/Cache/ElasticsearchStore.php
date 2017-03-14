<?php namespace ChaoticWave\BlueVelvet\Cache;

use Elasticsearch\Common\Exceptions\Conflict409Exception;
use Illuminate\Cache\TaggableStore;
use Carbon\Carbon;
use Illuminate\Contracts\Cache\Store;

class ElasticsearchStore extends TaggableStore implements Store
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @var string
     */
    const DEFAULT_INDEX = 'bvi_cache';

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @var \Elasticsearch\Client
     */
    protected $client;
    /**
     * @var string Cache prefix
     */
    protected $prefix;
    /**
     * @var string The cache's index
     */
    protected $index;
    /**
     * @var string The cache's document type
     */
    protected $type;

    /**
     * Create a new Elasticsearch store.
     *
     * @param  \Elasticsearch\Client $client
     * @param  string                $prefix
     */
    public function __construct($client, $prefix = null)
    {
        $this->client = $client;
        $this->index = config('cache.stores.elasticsearch.index', static::DEFAULT_INDEX);
        $this->type = config('cache.stores.elasticsearch.type');

        $this->setPrefix($prefix);
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        $_params = $this->buildParams(['id' => $this->prefix . $key]);

        try {
            return $_response = $this->client->get($_params);
        } catch (\Exception $_ex) {
        }

        return null;
    }

    /**
     * Retrieve multiple items from the cache by key.
     * Items not found in the cache will have a null value.
     *
     * @param array $keys
     *
     * @return array
     */
    public function many(array $keys)
    {
        $_prefixed = array_map(function($key) {
            return $this->prefix . $key;
        },
            $keys);

        $_params = $this->buildParams(['body' => $_prefixed]);

        try {
            $_responses = $this->client->mget($_params);

            return array_combine($keys, $_responses);
        } catch (\Exception $_ex) {
        }

        return null;
    }

    /**
     * Store an item in the cache for a given number of minutes.
     *
     * @param  string    $key
     * @param  mixed     $value
     * @param  float|int $minutes
     *
     * @return array|null|void
     */
    public function put($key, $value, $minutes)
    {
        $_document = new CachedDataDocument($this->index, ['cache_key' => $key, 'cache_value' => $value], $this->type, $minutes);
        $_document->setId($this->prefix . $key);
        $_params = $_document->toParamsArray(true, true);

        try {
            $_response = $this->client->index($_params);
        } catch (\Exception $_ex) {
        }

        return null;
    }

    /**
     * Store multiple items in the cache for a given number of minutes.
     *
     * @param  array     $values
     * @param  float|int $minutes
     */
    public function putMany(array $values, $minutes)
    {
        $_doc = new CachedDataDocument($this->index, [], $this->type, $minutes);
        $_params = ['refresh' => true,];

        foreach ($values as $_key => $_value) {
            $_params['body'][] = [
                'index' => [
                    '_index' => $_doc->getIndex(),
                    '_type'  => $_doc->getDocumentType(false),
                    '_id'    => $this->prefix . $_key,
                ],
            ];

            $_params['body'][] = ['cache_key' => $_key, 'cache_value' => $_value, 'expires' => $minutes];
        }

        try {
            $_responses = $this->client->bulk($_params);
        } catch (\Exception $_ex) {
        }
    }

    /**
     * Store an item in the cache if the key doesn't exist.
     *
     * @param  string    $key
     * @param  mixed     $value
     * @param  float|int $minutes
     *
     * @return bool
     */
    public function add($key, $value, $minutes)
    {
        try {
            $this->put($key, $value, $minutes);
        } catch (Conflict409Exception $_ex) {
            //  Already exists, forget
            return false;
        } catch (\Exception $_ex) {
            //  Error
            return false;
        }

        //  Worked!
        return true;
    }

    /**
     * Increment the value of an item in the cache.
     *
     * @param  string $key
     * @param  mixed  $value
     *
     * @return int|bool
     */
    public function increment($key, $value = 1)
    {
        $_value = false;

        try {
            $_doc = $this->get($key);
        } catch (\Exception $_ex) {
        }

        return $_value;
    }

    /**
     * Decrement the value of an item in the cache.
     *
     * @param  string $key
     * @param  mixed  $value
     *
     * @return int|bool
     */
    public function decrement($key, $value = 1)
    {
        $_value = false;

        try {
            $_doc = $this->get($key);
        } catch (\Exception $_ex) {
        }

        return $_value;
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param  string $key
     * @param  mixed  $value
     */
    public function forever($key, $value)
    {
        $this->put($key, $value, 0);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string $key
     *
     * @return bool
     */
    public function forget($key)
    {
        $this->client->delete([]);
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool
     */
    public function flush()
    {
        $this->client->indices()->delete(['index' => $this->index]);
    }

    /**
     * Get the UNIX timestamp for the given number of minutes.
     *
     * @param  int $minutes
     *
     * @return int
     */
    protected function toTimestamp($minutes)
    {
        return $minutes > 0 ? Carbon::now()->addSeconds($minutes * 60)->getTimestamp() : 0;
    }

    /**
     * Get the underlying Elasticsearch client
     *
     * @return \Elasticsearch\Client
     */
    public function getElasticsearch()
    {
        return $this->client;
    }

    /**
     * Get the cache key prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set the cache key prefix.
     *
     * @param  string $prefix
     *
     * @return void
     */
    public function setPrefix($prefix)
    {
        $this->prefix = !empty($prefix) ? $prefix . ':' : '';
    }

    /**
     * Builds basic params for ES client call
     *
     * @param array $data The param data
     * @param bool  $all  If true, and no $type is available, "_all" is used
     *
     * @return array
     */
    protected function buildParams(array $data, $all = true)
    {
        return array_merge([
            'index' => $this->index,
            'type'  => $this->type ?: ($all ? '_all' : null),
        ],
            $data);
    }

    /**
     * Tests a response array to see if the "acknowledged" bool is TRUE
     *
     * @param array $response The response
     *
     * @return bool
     */
    protected function acked($response)
    {
        return is_array($response) && array_get($response, 'acknowledged', false);
    }
}
