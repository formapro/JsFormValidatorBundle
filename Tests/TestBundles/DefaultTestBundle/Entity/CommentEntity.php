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
class CommentEntity
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
     * @ORM\Column(name="content", type="string", length=255)
     * @Assert\NotBlank(message="comment_message")
     */
    private $content;

    /**
     * @var TagEntity
     *
     * @ORM\ManyToOne(targetEntity="TaskEntity", inversedBy="comments")
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
     * Set content
     *
     * @param string $content
     *
     * @return TestEntity
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
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
