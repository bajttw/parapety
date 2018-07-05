<?php
namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Utils\Utils;
use FOS\UserBundle\Model\User as BaseUser;
/**
 * Users
 */
class Users extends BaseUser
{
    const en = 'users';
    const ec = 'Users';
    const idPrototype = '__uid__';

//  <editor-fold defaultstate="collapsed" desc="Fields utils">
    public static $dicNames = [
        'id' => 'v',
        'username' => 'n',
        'usernameCanonical' => 'd',
    ];

    public static $shortNames = [
        'id' => 'id',
        'usernameCanonical' => 'un',
        'username' => 'u',
        'enabled' => 'a',
        'lastLogin' => 'll',
        'roles' => 'r',
        'childs' => [
            'userGroups' => 'UsersGroups',
            'clients' => "Clients",
            'settings' => "Settings",
            'notes' => 'Notes'
        ],
    ];

    public static function getFields(?string $type = null):array
    {
        switch ($type) {
            case '':
            case 'list':
                $fields = array_diff(array_keys(static::$shortNames), ['childs']);
                break;
            default:
                $fields = array_keys(static::$dicNames);
        }
        return $fields;
    }

    public static function getChildEntityClass($name)
    {
        $dd = static::$shortNames;
        if ($childClass = Utils::deep_array_value(['childs', $name], static::$shortNames)) {
            return static::ns . $childClass;
        }
        return null;
    }

    public static function getChildShortNames(string $name):?array
    {
        if ($ec = self::getChildEntityClass($name)) {
            return $ec::$shortNames;
        }
        return null;
    }

    public static function getShortName($name)
    {
        $names = static::$shortNames;
        $keys = explode('.', $name);
        foreach ($keys as $k) {
            $short = array_key_exists($k, $names) ? $names[$k] : null;
            if ($childClass = Utils::deep_array_value(['childs', $k], $names)) {
                $ec = static::ns . $childClass;
                $names = $ec::$shortNames;
            }
        }
        return $short ? $short : $name;
    }

// </editor-fold>






    public function getSuccessFields(?string $type=null):array
    {
        return [];
    }

    public function getMessageDataFields(?string $type=null):array
    {
        return [];
    }

    public function getDeleteFields(?string $type=null):array
    {
        return ['id', 'usernameCanonical'];
    }


    public function getMessageData(?string $type=null, array $dataReturn=[]):array{
        $data=[];
        foreach($this->getMessageDataFields($type) as $f){
            $data[]=$this->getStrField($f);
        }
        return $data;
    }


    public function getSuccessData(?string $type=null):array
    {
        $data = [
            "fields" => [],
        ];
        switch ($type) {
            case 'create':
            case 'update':
                $data['entity_data'] = $this->getShowData();
            case 'remove':
            default:
        }
        $data['fields'] = $this->getFieldsStr($this->getSuccessFields($type));
        return $data;
    }

    public function getDataDelete():array
    {
        $data = [
            'id' => $this->getId(),
        ];
        return $data;
    }

    public function genMessage($msg, $entityClassName = null, $include = [])
    {
        if ($this->controller) {
            $msg = $this->controller->trans($this->controller->messageText($msg, $entityClassName ? $entityClassName : static::ec));
            if ($count = count($include)) {
                $search = [];
                for ($i = 1; $i <= $count; $i++) {
                    $search[] = '%' . $i;
                }
                $msg = str_replace($search, $include, $msg);
            }
        }
        return $msg;
    }

//  <editor-fold defaultstate="collapsed" desc="Variables">
    /**
     * @var integer
     */
    protected $id;

    // /**
    //  * @var string
    //  */
    // private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $userGroups;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $clients;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $settings;


    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $notes;
// </editor-fold>

    public function __construct($options = [])
    {
        parent::__construct();
        $this->userGroups = new ArrayCollection();
        $this->clients = new ArrayCollection();
        $this->settings = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $defaults = Utils::deep_array_value('defaults', $options);
        $this->controller = Utils::deep_array_value('controller', $options);
        if (is_array($defaults)) {
            foreach ($defaults as $name => $value) {
                $fnInit = 'init' . ucfirst($name);
                if (method_exists($this, $fnInit)) {
                    $this->$fnInit($this->controller, $value);
                }
            }
        }
    }

    public function __toString():string
    {
        return $this->id;
    }
// <editor-fold defaultstate="collapsed" desc="Fields functions">
    /**
     * Get id
     *
     * @return integer
     */
    public function getId():?int
    {
        return $this->id;
    }

    // /**
    //  * Set name
    //  *
    //  * @param string $name
    //  *
    //  * @return Users
    //  */
    // public function setName(string $name)
    // {
    //     $this->name = $name;

    //     return $this;
    // }

    // /**
    //  * Get name
    //  *
    //  * @return string
    //  */
    // public function getName():?string
    // {
    //     return $this->name;
    // }

    /**
     * Add userGroup
     *
     * @param \AppBundle\Entity\UsersGroups $userGroup
     *
     * @return Users
     */
    public function addUserGroup(\AppBundle\Entity\UsersGroups $userGroup)
    {
        $this->userGroups[] = $userGroup;

        return $this;
    }

    /**
     * Remove userGroup
     *
     * @param \AppBundle\Entity\UsersGroups $userGroup
     */
    public function removeUserGroup(\AppBundle\Entity\UsersGroups $userGroup)
    {
        $this->userGroups->removeElement($userGroup);
    }

    /**
     * Get userGroups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserGroups()
    {
        return $this->userGroups;
    }

    /**
     * Add client
     *
     * @param \AppBundle\Entity\Clients $client
     *
     * @return Users
     */
    public function addClient(\AppBundle\Entity\Clients $client)
    {
        $this->clients[] = $client;

        return $this;
    }

    /**
     * Remove client
     *
     * @param \AppBundle\Entity\Clients $client
     */
    public function removeClient(\AppBundle\Entity\Clients $client)
    {
        $this->clients->removeElement($client);
    }

    /**
     * Get clients
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClients()
    {
        return $this->clients;
    }

    public function hasClient(\AppBundle\Entity\Clients $client = null)
    {
        return $client ? $this->clients->contains($client) : false;
    }

    /**
     * Add setting
     *
     * @param \AppBundle\Entity\Settings $setting
     *
     * @return Users
     */
    public function addSetting(\AppBundle\Entity\Settings $setting)
    {
        $this->settings[] = $setting;

        return $this;
    }

    /**
     * Remove setting
     *
     * @param \AppBundle\Entity\Settings $setting
     */
    public function removeSetting(\AppBundle\Entity\Settings $setting)
    {
        $this->settings->removeElement($setting);
    }

    /**
     * Get settings
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Add note
     *
     * @param \AppBundle\Entity\Notes $note
     *
     * @return Users
     */
    public function addNote(\AppBundle\Entity\Notes $note)
    {
        $this->notes[] = $note;

        return $this;
    }

    /**
     * Remove note
     *
     * @param \AppBundle\Entity\Notes $note
     */
    public function removeNote(\AppBundle\Entity\Notes $note)
    {
        $this->notes->removeElement($note);
    }

    /**
     * Get notes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotes()
    {
        return $this->notes;
    }
// </editor-fold>

    public function getLastLoginStr()
    {
        if ($this->lastLogin) {
            return $this->lastLogin->format("Y-m-d");
        } else {
            return "";
        }
    }

}
