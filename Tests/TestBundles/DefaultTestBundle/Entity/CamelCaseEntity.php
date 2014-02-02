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
class CamelCaseEntity
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
     * @ORM\Column(name="camel_case_field", type="string", length=255)
     * @Assert\NotBlank(message="camel_case_field_message")
     */
    private $camelCaseField;

    /**
     * Get Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get CamelCaseField
     *
     * @return string
     */
    public function getCamelCaseField()
    {
        return $this->camelCaseField;
    }

    /**
     * Set camelCaseField
     *
     * @param string $camelCaseField
     *
     * @return CamelCaseEntity
     */
    public function setCamelCaseField($camelCaseField)
    {
        $this->camelCaseField = $camelCaseField;

        return $this;
    }
}
