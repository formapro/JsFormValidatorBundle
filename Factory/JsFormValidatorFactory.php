<?php
/**
 * Created by PhpStorm.
 * User: Yury Maltsev
 * Email: dev.ymalcev@gmail.com
 * Date: 10/24/13
 * Time: 3:14 PM
 */

namespace Fp\JsFormValidatorBundle\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use Fp\JsFormValidatorBundle\Validator\Constraints\Repeated;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Mapping\GetterMetadata;
use Symfony\Component\Validator\Mapping\PropertyMetadata;

class JsFormValidatorFactory {
    /**
     * @var string
     */
    public $id;

    /**
     * @var array
     */
    public $groups = ['Default'];

    /**
     * @var array
     */
    public $fields = [];

    /**
     * @var array
     */
    public $getters = [];

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var array
     */
    private $config;

    /**
     * @param string $id
     * @param array $groups
     * @param array $config
     * @param null|Translator $translator
     * @param string $domain
     */
    public function __construct($id, array $groups, $config = [], $translator = null, $domain = 'validators')
    {
        $this->id         = $id;
        $this->groups     = array_merge($groups, $this->groups);
        $this->config     = $config;
        $this->translator = $translator;
        $this->domain     = $domain;
    }

    public function getJsonMethods()
    {
        return json_encode($this->getters);
    }

    /**
     * Add constraints for the specified field
     *
     * @param FormView|PropertyMetadata $field
     *
     * @throws \Symfony\Component\Form\Exception\UnexpectedTypeException
     *
     * @return $this
     */
    public function addField($field)
    {
        $type = null;

        // If this field is a child of Entity
        if ($field instanceof PropertyMetadata) {
            $id = $this->id . '_' . $field->getName();
            $constraints = (array) $field->getConstraints();

        // If this field is a child of Form
        } elseif ($field instanceof FormView) {
            $opts = new ArrayCollection($field->vars);
            $id = $opts->get('id');
            $constraints = (array) $opts->get('constraints');
            $type = $opts->get('type');

        // Do not allow other instances
        } else {
            throw new UnexpectedTypeException($field, 'Symfony\Component\Form\FormView, Symfony\Component\Validator\Mapping\PropertyMetadata');
        }

        // Actually, comparing of two fields (e.g. 'password' and 'confirm password') is not a constraint in Symfony -
        // it is implemented as RepeatedType that has its own validator (without using of any constraints).
        // To unify the functionality on our side, we will create a fake constraint Repeated for this field type,
        // that will be attached to the first element and has links to other related elements and necessary messages.
        $repeatedType = new RepeatedType();
        if ($type == $repeatedType->getName()) {
            $constr = new Repeated();
            $constr->populate($field);
            $constraints[] = $constr;
            $id = $constr->getId();
        }

        // Process the constraints and save to own property
        /** @var $constr Constraint */
        foreach ($constraints as $constr) {
            // Check if constraint is in the allowed groups
            $mutual = array_intersect($this->groups, $constr->groups);
            if (!empty($mutual)) {
                // Simplify a full class name to a string without slashes
                $constrName = str_replace('\\', '', get_class($constr));
                // Translate messages if need and add to storage
                $this->fields[$id][$constrName][] = $this->translateMessages($constr);
            }
        }

        return $this;
    }

    /**
     * Add constraints for the specified method
     *
     * @param GetterMetadata $getter
     *
     * @return $this
     */
    public function addGetter(GetterMetadata $getter)
    {
        $constraints = (array) $getter->getConstraints();
        /** @var $constr Constraint */
        foreach ($constraints as $constr) {
            // Check if constraint is in the allowed groups
            $mutual = array_intersect($this->groups, $constr->groups);
            if (!empty($mutual)) {
                // Save constraints to the 'getters' bag by schema: ClassName => MethodName => constraints
                $entityName = str_replace('\\', '', $getter->getClassName());
                $constrName = str_replace('\\', '', get_class($constr));
                $this->getters[$entityName][$getter->getName()][$constrName][] = $this->translateMessages($constr);
            }
        }
    }

    /**
     * @return string
     */
    public function generateJavascript()
    {
        $resultJs = $this->createJsFormValidator() . "\n\n";
        // For fields
        foreach ($this->fields as $id => $value) {
            $resultJs .= $this->createJsFieldValidator($id) . "\n";
        }

        return "<script type='text/javascript'>".$resultJs."</script>";
    }

    /**
     * @return string
     */
    public function createJsFormValidator()
    {
        $config = [
            'fields' => array_keys($this->fields)
        ];
        if (isset($this->config[$this->id])) {
            $config = array_merge($config, $this->config[$this->id]);
        }
        $config = json_encode($config);
        $getters = json_encode($this->getters);

        return 'new FpJsFormValidator("' . $this->id . '", JSON.parse(\'' . $getters . '\'), JSON.parse(\'' . $config . '\'));';
    }

    /**
     * @param $name
     *
     * @return string
     */
    public function createJsFieldValidator($name)
    {
        $config = [];
        if (isset($this->config[$name])) {
            $config = array_merge($config, $this->config[$name]);
        }
        $config = json_encode($config);
        $constr = json_encode($this->fields[$name]);

        return 'new FpJsFieldValidator("' . $name . '", JSON.parse(\'' . $constr . '\'), JSON.parse(\'' . $config . '\'));';
    }

    /**
     * Translage messages in a constarint
     *
     * @param Constraint $constraint
     *
     * @return Constraint
     */
    private function translateMessages(Constraint $constraint) {
        if (null !== $this->translator) {
            // Translate each string that contains the substring 'message'
            foreach ($constraint as $propName => $propValue) {
                if (false !== strpos(strtolower($propName), 'message')) {
                    $constraint->{$propName} = $this->translator->trans($propValue, [], $this->domain);
                }
            }
        }

        return $constraint;
    }
} 