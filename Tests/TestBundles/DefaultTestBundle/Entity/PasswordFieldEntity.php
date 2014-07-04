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
class PasswordFieldEntity
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
     * @var integer
     *
     * @ORM\Column(name="password", type="string", length=255)
     * @Assert\NotBlank(message="pass_not_blank_message")
     * @Assert\Length(min="3", minMessage="pass_min_length_message")
     */
    private $password;

    /**
     * Get Password
     *
     * @return int
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password
     *
     * @param int $password
     *
     * @return PasswordFieldEntity
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
