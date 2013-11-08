<?php

namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Address
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Address
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
     * @ORM\Column(name="timezone", type="string", length=255)
     */
    private $timezone;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="addressLine", type="string", length=255)
     */
    private $addressLine;

    /**
     * @var User
     *
     * @ORM\ManyToOne(
     *     targetEntity="User",
     *     inversedBy="addresses"
     * )
     * @ORM\JoinColumn(
     *     name="user_id",
     *     referencedColumnName="id"
     * )
     */
    protected $user;


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
     * Set timezone
     *
     * @param string $timezone
     * @return Address
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    
        return $this;
    }

    /**
     * Get timezone
     *
     * @return string 
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return Address
     */
    public function setCountry($country)
    {
        $this->country = $country;
    
        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Address
     */
    public function setCity($city)
    {
        $this->city = $city;
    
        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set addressLine
     *
     * @param string $addressLine
     * @return Address
     */
    public function setAddressLine($addressLine)
    {
        $this->addressLine = $addressLine;
    
        return $this;
    }

    /**
     * Get addressLine
     *
     * @return string 
     */
    public function getAddressLine()
    {
        return $this->addressLine;
    }

    /**
     * Get User
     *
     * @return \Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set user
     *
     * @param \Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\User $user
     *
     * @return Address
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }
}
