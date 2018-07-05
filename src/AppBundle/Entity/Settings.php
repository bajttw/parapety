<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Utils\Utils as Utils;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Settings
 */
class Settings extends AppEntity
{
    const en = 'settings';
    const ec = 'Settings';
    const idPrototype = '__sid__';

    //  <editor-fold defaultstate="collapsed" desc="Fields">
    public static $shortNames = [
        'id' => 'id',
        'client' => 'cl',
        'name' => 'n',
        'value' => 'v',
        'description' => 'd',
        'childs' => [
            'client' => 'Clients',
        ],
    ];

    public static function getFields(?string $type = null):array
    {
        switch ($type) {
            case '':
                $fields = parent::getFields($type);
                break;
            case 'list':
                $fields = [
                    'id',
                    [
                        'name' => 'client',
                        'joinField' => [
                            ['name' => 'name'],
                            ['name' => 'code'],
                        ],
                    ],
                    'name',
                    'value',
                    'description',
                ];
                break;
            default:
                $fields = ['id', 'name', 'value'];
        }
        return $fields;
    }

    public function getSuccessFields(?string $type=null):array
    {
        $fields = [];
        switch ($type) {
            case 'create':
                $fields = ['name'];
                break;
            case 'update':
                break;
            case 'remove':
            default:
        }
        return array_replace(parent::getSuccessFields($type), $fields);
    }

    public function getDeleteFields(?string $type=null):array
    {
        return array_replace(
            parent::getDeleteFields($type), 
            ['name', 'client.name']
        );
    }


    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Variables">

    private $id;
    private $client;
    private $name;
    private $value;
    private $description;
    // </editor-fold>

    public function getArray($options = [])
    {
        $asArray = array_key_exists('asArray', $options) ? $options['asArray'] : true;
        $shortNames = array_key_exists('shortNames', $options) ? $options['shortNames'] : false;
        $fields = self::$shortNames;
        $keys = ['id', 'name', 'value', 'description'];
        $data = [];
        foreach ($keys as $key) {
            if ($shortNames) {
                $data[$fields[$key]] = $this->$key;
            } else {
                $data[$key] = $this->$key;
            }
        }
        return $asArray ? $data : \json_encode($data);
    }

    // <editor-fold defaultstate="collapsed" desc="Fields functions">
    public function getId():?int
    {
        return $this->id;
    }

    public function setName(string $name)
    {
        $this->name = strtolower($name);
        return $this;
    }

    public function getName():?string
    {
        return $this->name;
    }

    /**
     * Set client
     *
     * @param \AppBundle\Entity\Clients $client
     *
     * @return Settings
     */
    public function setClient(\AppBundle\Entity\Clients $client = null)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \AppBundle\Entity\Clients
     */
    public function getClient()
    {
        return $this->client;
    }

    public function setValue($value)
    {
        $this->value = is_array($value) ? json_encode($value) : $value;
        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function decodeValue()
    {
        return json_decode($this->value, true);
    }

    public function setDescription(?string $description)
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription():?string
    {
        return $this->description;
    }

    // </editor-fold>
}
