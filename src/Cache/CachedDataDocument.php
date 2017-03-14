<?php namespace ChaoticWave\BlueVelvet\Cache;

use Carbon\Carbon;
use ChaoticWave\BlueVelvet\Utility\Uri;
use Illuminate\Support\Collection;

class CachedDataDocument extends Collection
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @var int The default number of minutes to keep cached data
     */
    const DEFAULT_TTL = 5;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @var string The index of this document
     */
    protected $index;
    /**
     * @var string The type of this document
     */
    protected $type;
    /**
     * @var string The document ID
     */
    protected $id;
    /**
     * @var array Index type/field mappings
     */
    protected $mapping;
    /**
     * @var int TTL time in minutes
     */
    protected $expire = self::DEFAULT_TTL;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Constructor
     *
     * @param string      $index
     * @param array|mixed $items
     * @param string|null $type
     * @param int         $expire
     */
    public function __construct($index, $items = [], $type = null, $expire = self::DEFAULT_TTL)
    {
        $this->expire = $expire;

        //  Boot the document
        $this->boot($index, $items, $type);

        //  Load the rest into the collection
        parent::__construct($items);
    }

    /**
     * A chance to massage the items before the go in the collection
     *
     * @param string      $index
     * @param array       $items
     * @param string|null $type
     *
     * @return array
     */
    protected function boot($index, &$items = [], $type = null)
    {
        $this->index = $index;
        !empty($type) and $this->type = $type;

        foreach ($items as $_key => $_value) {
            $items[$_key] = $_value;

            if ($_value !== '0000-00-00' && $_value !== '00/00/00') {
                if ('date' === strtolower(substr($_key, -4))) {
                    $items[$_key] = Carbon::parse($_value)->toIso8601String();
                }
            }
        }

        return $items;
    }

    /**
     * Builds a $params array for use with the Elasticsearch client. Includes index, id, and body elements
     *
     * @param bool       $refresh If true, a "refresh" => true is added
     * @param bool       $upsert  If true, "body" will contain {"doc":"old body", "doc_as_upsert":true}
     * @param array|null $merge   Additional data to merge with params array. This method's data takes precedence
     *
     * @return array
     */
    public function toParamsArray($refresh = true, $upsert = false, $merge = null)
    {
        //  Make sure we are in UTF8
        $_data = array_map('utf8_encode', $this->toArray());

        $_body = $upsert ? [
            'doc'           => $_data,
            'doc_as_upsert' => true,
        ] : $_data;

        if (null !== $this->expire) {
            $_body['expires'] = $this->expire;
        }

        $_params = [
            'index' => $this->getIndex(),
            'body'  => $_body,
        ];

        if ($refresh) {
            $_params['refresh'] = true;
        }

        if (!empty($_id = $this->getId())) {
            $_params['id'] = $_id;
        }

        if (!empty($merge) && is_array($merge)) {
            $_params = array_merge($merge, $_params);
        }

        //  Add in the type cuz we know it's there
        $_params['type'] = $this->getDocumentType();

        return $_params;
    }

    /**
     * Returns the URI of this document in "/index/type/id" format
     *
     * @return null|string
     */
    public function getDocumentUri()
    {
        return Uri::segment([$this->index, $this->getDocumentType(), $this->id]);
    }

    /** @inheritdoc */
    public function getMapping()
    {
        //  Dynamically map document
        foreach ($this->all() as $_key => $_value) {
            //  Map non-scalar fields
            if (null !== $_value && !is_scalar($_value)) {
                $this->mapping['properties'][$_key] = ['type' => 'object', 'dynamic' => true];
            }
        }

        return $this->mapping;
    }

    /**
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param string $index
     *
     * @return $this
     */
    public function setIndex($index)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param bool $all
     *
     * @return string
     */
    public function getDocumentType($all = true)
    {
        return $this->type ?: ($all ? '_all' : null);
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setDocumentType($type)
    {
        $this->type = $type;

        return $this;
    }
}
