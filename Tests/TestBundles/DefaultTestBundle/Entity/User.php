<?php

namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\UserRepository")
 */
class User
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=50)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var integer
     *
     * @ORM\Column(name="age", type="integer")
     */
    private $age;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=1)
     */
    private $gender;

    /**
     * @var array
     *
     * @ORM\Column(name="languages", type="array")
     */
    private $languages;

    /**
     * @var boolean
     *
     * @ORM\Column(name="married", type="boolean")
     */
    private $married;

    /**
     * @var array
     *
     * @ORM\Column(name="os", type="array")
     */
    private $os;

    /**
     * @var string
     *
     * @ORM\Column(name="site", type="string", length=255)
     */
    private $site;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=15)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar", type="string", length=255)
     */
    private $avatar;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(
     *     targetEntity="Book",
     *     inversedBy="users"
     * )
     * @ORM\JoinTable(
     *     name="m2m_user_book",
     *     joinColumns={@ORM\JoinColumn(
     *         name="user_id",
     *         referencedColumnName="id"
     *     )},
     *     inverseJoinColumns={@ORM\JoinColumn(
     *         name="book_id",
     *         referencedColumnName="id"
     *     )}
     * )
     */
    protected $books;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Address",
     *      mappedBy="user",
     *      cascade={"persist", "remove"}
     * )
     */
    protected $addresses;

    /**
     * @var Role
     *
     * @ORM\ManyToOne(
     *     targetEntity="Role",
     *     inversedBy="user"
     * )
     * @ORM\JoinColumn(
     *     name="role_id",
     *     referencedColumnName="id",
     *     nullable=true
     * )
     */
    protected $role;


    function __construct()
    {
        $this->addresses = new ArrayCollection();
        $this->books     = new ArrayCollection();
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set age
     *
     * @param integer $age
     * @return User
     */
    public function setAge($age)
    {
        $this->age = $age;
    
        return $this;
    }

    /**
     * Get age
     *
     * @return integer 
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    
        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set languages
     *
     * @param array $languages
     * @return User
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;
    
        return $this;
    }

    /**
     * Get languages
     *
     * @return array 
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Set married
     *
     * @param boolean $married
     * @return User
     */
    public function setMarried($married)
    {
        $this->married = $married;
    
        return $this;
    }

    /**
     * Get married
     *
     * @return boolean 
     */
    public function getMarried()
    {
        return $this->married;
    }

    /**
     * Set os
     *
     * @param array $os
     * @return User
     */
    public function setOs($os)
    {
        $this->os = $os;
    
        return $this;
    }

    /**
     * Get os
     *
     * @return array 
     */
    public function getOs()
    {
        return $this->os;
    }

    /**
     * Set site
     *
     * @param string $site
     * @return User
     */
    public function setSite($site)
    {
        $this->site = $site;
    
        return $this;
    }

    /**
     * Get site
     *
     * @return string 
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return User
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    
        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    
        return $this;
    }

    /**
     * Get avatar
     *
     * @return string 
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Get Books
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getBooks()
    {
        return $this->books;
    }

    /**
     * Add book
     *
     * @param Book $book
     *
     * @return User
     */
    public function addBook(Book $book)
    {
        $this->books->add($book);

        return $this;
    }

    /**
     * Remove book
     *
     * @param Book $book
     *
     * @return User
     */
    public function removeBook(Book $book)
    {
        $this->books->remove($book);

        return $this;
    }

    /**
     * Get Addresses
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * Add address
     *
     * @param Address $address
     *
     * @return User
     */
    public function addAddress(Address $address)
    {
        $this->addresses->add($address);

        return $this;
    }

    /**
     * Remove address
     *
     * @param Address $address
     *
     * @return User
     */
    public function removeAddress(Address $address)
    {
        $this->addresses->remove($address);

        return $this;
    }

    public static function getGendersList()
    {
        return array(
            'm' => 'Male',
            'f' => 'Female',
        );
    }

    public static function getOsList()
    {
        return array(
            'w' => 'Windows',
            'm' => 'Mac OS',
            'l' => 'Linux',
        );
    }

    /**
     * Get Role
     *
     * @return \Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set role
     *
     * @param \Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\Role $role
     *
     * @return User
     */
    public function setRole($role = null)
    {
        $this->role = $role;

        return $this;
    }
}
