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
class CustomizationEntity
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
     * @ORM\Column(name="disabled", type="string", length=255)
     * @Assert\NotBlank(message="disabled_field_message")
     */
    private $disabled;

    /**
     * @var string
     *
     * @ORM\Column(name="show_errors", type="string", length=255)
     * @Assert\NotBlank(message="show_errors_message")
     */
    private $showErrors;

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
     * Get Disabled
     *
     * @return string
     */
    public function getDisabled()
    {
        return $this->disabled;
    }

    /**
     * Set disabled
     *
     * @param string $disabled
     *
     * @return CustomizationEntity
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;

        return $this;
    }

    /**
     * Get ShowErrors
     *
     * @return string
     */
    public function getShowErrors()
    {
        return $this->showErrors;
    }

    /**
     * Set showErrors
     *
     * @param string $showErrors
     *
     * @return CustomizationEntity
     */
    public function setShowErrors($showErrors)
    {
        $this->showErrors = $showErrors;

        return $this;
    }
}
