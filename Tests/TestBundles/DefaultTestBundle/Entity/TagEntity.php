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
class TagEntity
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
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank(message="tag_message")
     */
    private $title;

    /**
     * @var TagEntity
     *
     * @ORM\ManyToOne(targetEntity="TaskEntity", inversedBy="tags")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id")
     */
    private $task;


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
     * Set title
     *
     * @param string $title
     *
     * @return TestEntity
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get Task
     *
     * @return \Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\TaskEntity
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * Set task
     *
     * @param \Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\TaskEntity $task
     *
     * @return TagEntity
     */
    public function setTask($task)
    {
        $this->task = $task;

        return $this;
    }
}
