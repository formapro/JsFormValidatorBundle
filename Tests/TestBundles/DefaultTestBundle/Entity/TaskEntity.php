<?php

namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Book
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class TaskEntity
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\TagEntity", mappedBy="task")
     * @Assert\Count(
     *     min=3,
     *     minMessage="collection_min_count_message"
     * )
     */
    private $tags;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\CommentEntity", mappedBy="task")
     */
    private $comments;

    public function __construct()
    {
        $this->tags     = new ArrayCollection();
        $this->comments = new ArrayCollection();
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
     * Get Tags
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set tags
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tags
     *
     * @return TaskEntity
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @param TagEntity $tag
     */
    public function addTag($tag)
    {
        $this->tags->add($tag);
    }

    /**
     * Get Comments
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set comments
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $comments
     *
     * @return TaskEntity
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * @param CommentEntity $comment
     */
    public function addComment($comment)
    {
        $this->comments->add($comment);
    }
}
