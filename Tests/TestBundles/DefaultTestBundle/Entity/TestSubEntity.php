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
class TestSubEntity
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
     * @Assert\NotBlank(message="sub_entity_no_groups_message")
     * @Assert\NotBlank(
     *     message="sub_entity_groups_array_message",
     *     groups={"groups_array"}
     * )
     * @Assert\NotBlank(
     *     message="sub_entity_groups_child_message",
     *     groups={"groups_child"}
     * )
     */
    private $name;

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
     * @return bool
     * @Assert\IsTrue(
     *     message="sub_entity_getter_groups_child_message",
     *     groups={"groups_child"}
     * )
     * @Assert\IsTrue(
     *     message="sub_entity_getter_groups_array_message",
     *     groups={"groups_array"}
     * )
     * @Assert\IsTrue(
     *     message="sub_entity_getter_no_groups_message"
     * )
     */
    public function isNameValid()
    {
        return false;
    }
}
