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
class TestEntity
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
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank(message="entity_no_groups_message")
     * @Assert\NotBlank(
     *     message="entity_groups_array_message",
     *     groups={"groups_array"}
     * )
     * @Assert\NotBlank(
     *     message="entity_groups_callback_message",
     *     groups={"groups_callback"}
     * )
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\Type(
     *     type="integer",
     *     message="entity_groups_array_message",
     *     groups={"groups_array"}
     * )
     * @Assert\Type(
     *     type="integer",
     *     message="entity_groups_child_message",
     *     groups={"groups_child"}
     * )
     *
     * @ORM\Column(name="email", type="string", length=50)
     */
    private $email;

    /**
     * Field without any validate rules
     *
     * @var string
     * @ORM\Column(name="clear", type="string", length=50)
     */
    private $clear;

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
     *
     * @return TestEntity
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
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return bool
     * @Assert\IsTrue(
     *     message="getter_groups_array_message",
     *     groups={"groups_array"}
     * )
     * @Assert\IsTrue(
     *     message="getter_no_groups_message"
     * )
     */
    public function isNameValid()
    {
        return false;
    }

    /**
     * Get Clear
     *
     * @return string
     */
    public function getClear()
    {
        return $this->clear;
    }

    /**
     * Set clear
     *
     * @param string $clear
     *
     * @return TestEntity
     */
    public function setClear($clear)
    {
        $this->clear = $clear;

        return $this;
    }
}
