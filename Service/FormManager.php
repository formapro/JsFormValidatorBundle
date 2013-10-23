<?php
/**
 * Created by PhpStorm.
 * User: Yury Maltsev
 * Email: dev.ymalcev@gmail.com
 * Date: 10/22/13
 * Time: 10:51 AM
 */

namespace Fp\JsFormValidatorBundle\Service;


use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\PropertyMetadata;
use Symfony\Component\Validator\Validator;

class FormManager {
    /**
     * @var \Symfony\Component\Validator\Validator
     */
    protected $validator;

    /**
     * @var array
     */
    private $constraints = [];

    private $defaultGroups = ['Default'];

    /**
     * @param Validator $validator
     */
    function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function getAllConstraints(Form $form)
    {
        $this->constraints = [];
        $groups = (array) $form->getConfig()->getOption('validation_groups');
        $groups = array_merge($groups, $this->defaultGroups);
        $elements = $form->all();
        $entity = $form->getData();
        /** @var ClassMetadata $entityMetadata */
        $entityMetadata = !is_object($entity) ? null : $this->validator->getMetadataFactory()->getMetadataFor($entity);

        /** @var $elem Form */
        foreach ($elements as $name => $elem) {
            if ($entityMetadata && $entityMetadata->hasMemberMetadatas($name)) {
                $metadata = (array)$entityMetadata->getMemberMetadatas($name);
                /** @var $propertyData PropertyMetadata */
                foreach ($metadata as $propertyData) {
                    $this->addConstraintsToList($name, $propertyData->getConstraints(), $groups);
                }
            }

            if ($elem->getConfig()->hasOption('constraints')) {
                $this->addConstraintsToList($name, $elem->getConfig()->getOption('constraints'), $groups);
            }
        }

        return new ArrayCollection($this->constraints);
    }

    /**
     * @param string $name
     * @param array|Constraint $constraints
     * @param array $groups
     */
    private function addConstraintsToList($name, $constraints, $groups = [])
    {
        if (!is_array($constraints)) {
            $constraints = [$constraints];
        }

        /** @var $constr Constraint */
        foreach ($constraints as $constr) {
            $mutual = array_intersect($groups, $constr->groups);
            if (!empty($mutual)) {
                $this->constraints[$name][get_class($constr)] = $constr;
            }
        }
    }
} 