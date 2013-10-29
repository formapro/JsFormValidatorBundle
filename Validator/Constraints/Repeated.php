<?php
/**
 * Created by PhpStorm.
 * User: Yury Maltsev
 * Email: dev.ymalcev@gmail.com
 * Date: 10/24/13
 * Time: 1:06 PM
 */

namespace Fp\JsFormValidatorBundle\Validator\Constraints;


use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Constraint;

class Repeated extends Constraint {
    /**
     * @var string
     */
    public $message = 'The fields must match.';
    /**
     * @var array
     */
    public $relFields = [];

    /**
     * @var string
     */
    private $id;

    /**
     * @param FormView $elem
     *
     * @throws UnexpectedTypeException
     */
    public function populate(FormView $elem)
    {
        $opts = new ArrayCollection($elem->vars);
        $this->message = $opts->get('invalid_message');

        $children = $elem->children;

        $self     = array_shift($children);
        $selfOpts = new ArrayCollection($self->vars);
        $this->id = $selfOpts->get('id');

        /** @var $subItem FormView */
        foreach ($children as $subItem) {
            $subOpts = new ArrayCollection($subItem->vars);
            $this->relFields[] = $subOpts->get('id');
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
} 