<?php

namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Book
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class EmptyChoiceEntity
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
     * @ORM\Column(name="city", type="string", length=255)
     * @Assert\NotBlank(message="city_message")
     */
    private $city;

    /**
     * @var array
     *
     * @ORM\Column(name="country", type="string", length=255)
     * @Assert\NotBlank(message="country_message")
     */
    private $countries;

    /**
     * @var string
     *
     * @ORM\Column(name="continent", type="string", length=255)
     * @Assert\NotBlank(message="continent_message")
     */
    private $continent;

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
     * Get City
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return EmptyChoiceEntity
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get Countries
     *
     * @return array
     */
    public function getCountries()
    {
        return $this->countries;
    }

    /**
     * Set countries
     *
     * @param array $countries
     *
     * @return EmptyChoiceEntity
     */
    public function setCountries($countries)
    {
        $this->countries = $countries;

        return $this;
    }

    /**
     * @return string
     */
    public function getContinent() {
        return $this->continent;
    }

    /**
     * @param string $continent
     *
     * @return EmptyChoiceEntity
     */
    public function setContinent($continent) {
        $this->continent = $continent;
        return $this;
    }
}