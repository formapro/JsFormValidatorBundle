<?php
/**
 * Created by PhpStorm.
 * User: Yury Maltsev
 * Email: dev.ymalcev@gmail.com
 * Date: 11/12/13
 * Time: 4:17 PM
 */

namespace Fp\JsFormValidatorBundle\Tests\Fixtures;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entity
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Entity {
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
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="file", type="string", length=50)
     * @Assert\NotBlank()
     */
    private $file;

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
     * @return Entity
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @Assert\True(message = "wrong_name")
     */
    public function isNameLegal()
    {
        return $this->getName() != $this->getId();
    }

    /**
     * @Assert\True(message = "wrong_name")
     */
    public function isFileLegal()
    {
        return $this->getFile() != $this->getId();
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set file
     *
     * @param $file
     *
     * @return Entity
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }
} 