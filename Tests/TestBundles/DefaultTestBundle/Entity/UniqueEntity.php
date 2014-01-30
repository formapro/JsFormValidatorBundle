<?php

namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use /** @noinspection PhpUnusedAliasInspection */
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity as UniqueEntityConstraint;

/**
 * Book
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\UniqueRepository")
 *
 * @UniqueEntityConstraint(
 *     fields={"email"},
 *     message="single_unique_value"
 * )
 * @UniqueEntityConstraint(
 *     fields={"email", "name"},
 *     message="multiple_unique_value"
 * )
 * @UniqueEntityConstraint(
 *     fields={"email", "name"},
 *     errorPath="email",
 *     message="multiple_unique_value_with_error_path_email"
 * )
 * @UniqueEntityConstraint(
 *     fields={"email", "name"},
 *     errorPath="name",
 *     message="multiple_unique_value_with_error_path_name"
 * )
 */
class UniqueEntity
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
     * @ORM\Column(name="email", type="string", length=50)
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=50)
     *
     * @Assert\NotBlank(message="not_blank_value")
     */
    private $title;

    /**
     * @param array $data
     */
    public function populate(array $data)
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
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
     * Set email
     *
     * @param string $email
     *
     * @return BasicConstraintsEntity
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get Email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return UniqueEntity
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return UniqueEntity
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }
}
