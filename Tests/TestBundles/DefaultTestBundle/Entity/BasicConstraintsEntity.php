<?php

namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use /** @noinspection PhpUnusedAliasInspection */
    Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity as BaseUniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Book
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\BasicConstraintsRepository")
 *
 * @Assert\Callback({"Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Validator\ExternalValidator", "validateStaticCallback"})
 * @Assert\Callback({"Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Validator\ExternalValidator", "validateDirectStaticCallback"})
 */
class BasicConstraintsEntity
{
    public $isValid;
    protected function _t_get($trueVal, $falseVal)
    {
        return $this->isValid ? $trueVal : $falseVal;
    }

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
     * @Assert\Blank(message="blank_{{ value }}")
     */
    private $blank;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="not_blank_value")
     */
    private $notBlank;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=50)
     *
     * @Assert\Email(message="email_{{ value }}")
     */
    private $email;

    /**
     * @var string
     *
     * @Assert\Url(message="url_{{ value }}")
     */
    private $url;

    /**
     * @var string
     *
     * @Assert\Regex(
     *     message="regex_{{ value }}",
     *     pattern="/^a{3}\d+$/"
     * ),
     * @Assert\Regex(
     *     message="test_two_{{ value }}",
     *     pattern="#^a{3}\d+$#"
     * )
     */
    private $regex;

    /**
     * @var string
     *
     * @Assert\Ip(message="ip_{{ value }}")
     */
    private $ip;

    /**
     * @var string
     *
     * @Assert\Time(message="time_{{ value }}")
     */
    private $time;

    /**
     * @var string
     *
     * @Assert\Date(message="date_{{ value }}")
     */
    private $date;

    /**
     * @var string
     *
     * @Assert\DateTime(message="datetime_value")
     */
    private $datetime;

    /**
     * @param array $data
     */
    public function populate(array $data)
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * @return array
     */
    public static function getChoicesList()
    {
        return array('June', 'July', 'August');
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
     * Get Blank
     *
     * @return string
     */
    public function getBlank()
    {
        return $this->blank;
    }

    /**
     * Set blank
     *
     * @param string $blank
     *
     * @return BasicConstraintsEntity
     */
    public function setBlank($blank)
    {
        $this->blank = $blank;

        return $this;
    }

    /**
     * Get NotBlank
     *
     * @return string
     */
    public function getNotBlank()
    {
        return $this->notBlank;
    }

    /**
     * Set notBlank
     *
     * @param string $notBlank
     *
     * @return BasicConstraintsEntity
     */
    public function setNotBlank($notBlank)
    {
        $this->notBlank = $notBlank;

        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return BasicConstraintsEntity
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get Email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return BasicConstraintsEntity
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get Url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set regex
     *
     * @param string $regex
     *
     * @return BasicConstraintsEntity
     */
    public function setRegex($regex)
    {
        $this->regex = $regex;

        return $this;
    }

    /**
     * Get Regex
     *
     * @return string
     */
    public function getRegex()
    {
        return $this->regex;
    }

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return BasicConstraintsEntity
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get Ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set time
     *
     * @param string $time
     *
     * @return BasicConstraintsEntity
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get Time
     *
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }


    /**
     * Get Date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date
     *
     * @param string $date
     *
     * @return BasicConstraintsEntity
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Set datetime
     *
     * @param string $datetime
     *
     * @return BasicConstraintsEntity
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Get Datetime
     *
     * @return string
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @return bool
     * @Assert\IsTrue(message="true_value")
     */
    public function isTrue()
    {
        return $this->_t_get(true, false);
    }

    /**
     * @return bool
     * @Assert\IsFalse(message="false_value")
     */
    public function isFalse()
    {
        return $this->_t_get(false, true);
    }

    /**
     * @return bool
     * @Assert\IsNull(message="null_{{ value }}")
     */
    public function isNull()
    {
        return $this->_t_get(null, 1);
    }

    /**
     * @return bool
     * @Assert\NotNull(message="not_null_value")
     */
    public function isNotNull()
    {
        return $this->_t_get(1, null);
    }

    /**
     * @return int
     * @Assert\EqualTo(message="{{ value }}_equalTo_{{ compared_value }}", value=1)
     */
    public function isEqualTo()
    {
        return $this->_t_get("1", "0");
    }

    /**
     * @return int
     * @Assert\NotEqualTo(message="{{ value }}_notEqualTo_{{ compared_value }}", value=1)
     */
    public function isNotEqualTo()
    {
        return $this->_t_get("0", "1");
    }

    /**
     * @return int
     * @Assert\IdenticalTo(message="{{ value }}_identicalTo_{{ compared_value }}", value=1)
     */
    public function isIdenticalTo()
    {
        return $this->_t_get(1, "1");
    }

    /**
     * @return int
     * @Assert\NotIdenticalTo(message="{{ value }}_notIdenticalTo_{{ compared_value }}", value=1)
     */
    public function isNotIdenticalTo()
    {
        return $this->_t_get("1", 1);
    }

    /**
     * @return int
     * @Assert\LessThan(message="{{ value }}_lessThan_{{ compared_value }}", value=1)
     */
    public function isLessThan()
    {
        return $this->_t_get(0, 1);
    }

    /**
     * @return int
     * @Assert\LessThanOrEqual(message="{{ value }}_lessThanOrEqual_{{ compared_value }}", value=1)
     */
    public function isLessThanOrEqual()
    {
        return $this->_t_get(1, 2);
    }

    /**
     * @return int
     * @Assert\GreaterThan(message="{{ value }}_greaterThan_{{ compared_value }}", value=1)
     */
    public function isGreaterThan()
    {
        return $this->_t_get(2, 1);
    }

    /**
     * @return int
     * @Assert\GreaterThanOrEqual(message="{{ value }}_greaterThanOrEqual_{{ compared_value }}", value=1)
     */
    public function isGreaterThanOrEqual()
    {
        return $this->_t_get(1, 0);
    }

    /**
     * @return string
     * @Assert\Length(
     *     min=6,
     *     minMessage="value_minLength_singular_{{ limit }}|value_minLength_plural_{{ limit }}"
     * )
     */
    public function isLengthMin()
    {
        return $this->_t_get('long_pass', 'a');
    }

    /**
     * @return string
     * @Assert\Length(
     *     max=1,
     *     maxMessage="value_maxLength_singular_{{ limit }}|value_maxLength_plural_{{ limit }}"
     * )
     */
    public function isLengthMax()
    {
        return $this->_t_get('a', 'aa');
    }

    /**
     * @return string
     * @Assert\Length(
     *     min=1,
     *     max=1,
     *     exactMessage="value_exactLength_singular_{{ limit }}|value_exactLength_plural_{{ limit }}"
     * )
     */
    public function isLengthExact()
    {
        return $this->_t_get('a', 'aa');
    }

    /**
     * @return array
     * @Assert\Count(
     *     min=3,
     *     minMessage="value_minCount_singular_{{ limit }}|value_minCount_plural_{{ limit }}"
     * )
     */
    public function isCountMin()
    {
        return $this->_t_get(array('a', 'b', 'c', 'd'), array());
    }

    /**
     * @return array
     * @Assert\Count(
     *     max=1,
     *     maxMessage="value_maxCount_singular_{{ limit }}|value_maxCount_plural_{{ limit }}"
     * )
     */
    public function isCountMax()
    {
        return $this->_t_get(array('a'), array('a', 'b'));
    }

    /**
     * @return array
     * @Assert\Count(
     *     min=1,
     *     max=1,
     *     exactMessage="value_exactCount_singular_{{ limit }}|value_exactCount_plural_{{ limit }}"
     * )
     */
    public function isCountExact()
    {
        return $this->_t_get(array('a'), array('a', 'a'));
    }

    /**
     * @return int
     * @Assert\Range(
     *     min=1,
     *     minMessage="value_minRange_{{ limit }}"
     * )
     */
    public function isRangeMin()
    {
        return $this->_t_get(2, 0);
    }

    /**
     * @return int
     * @Assert\Range(
     *     max=1,
     *     maxMessage="value_maxRange_{{ limit }}"
     * )
     */
    public function isRangeMax()
    {
        return $this->_t_get(1, 2);
    }

    /**
     * @return int
     * @Assert\Range(
     *     min=1,
     *     min=1,
     *     invalidMessage="value_invalidRangeValue"
     * )
     */
    public function isRangeValueValid()
    {
        return $this->_t_get(1, 'a');
    }

    /**
     * @return array
     * @Assert\Type(
     *     type="array",
     *     message="{{ value }}_is_not_{{ type }}"
     * )
     */
    public function isTypeArray()
    {
        return $this->_t_get(array('a'), 'a');
    }

    /**
     * @return bool
     * @Assert\Type(
     *     type="boolean",
     *     message="{{ value }}_is_not_{{ type }}"
     * )
     */
    public function isTypeBool()
    {
        return $this->_t_get(true, 'a');
    }

    /**
     * @return \Closure
     * @Assert\Type(
     *     type="callable",
     *     message="{{ value }}_is_not_{{ type }}"
     * )
     */
    public function isTypeCallable()
    {
        return $this->_t_get(function(){}, 'a');
    }

    /**
     * @return float
     * @Assert\Type(
     *     type="float",
     *     message="{{ value }}_is_not_{{ type }}"
     * )
     */
    public function isTypeFloat()
    {
        return $this->_t_get(1.5, 1);
    }

    /**
     * @return int
     * @Assert\Type(
     *     type="integer",
     *     message="{{ value }}_is_not_{{ type }}"
     * )
     */
    public function isTypeInteger()
    {
        return $this->_t_get(1, 1.5);
    }

    /**
     * @return null
     * @Assert\Type(
     *     type="null",
     *     message="{{ value }}_is_not_{{ type }}"
     * )
     */
    public function isTypeNull()
    {
        return $this->_t_get(null, 'a');
    }

    /**
     * @return int
     * @Assert\Type(
     *     type="numeric",
     *     message="{{ value }}_is_not_{{ type }}"
     * )
     */
    public function isTypeNumeric()
    {
        return $this->_t_get("1", 'a');
    }

    /**
     * @return \StdClass
     * @Assert\Type(
     *     type="object",
     *     message="{{ value }}_is_not_{{ type }}"
     * )
     */
    public function isTypeObject()
    {
        return $this->_t_get(new \StdClass(), 'a');
    }

    /**
     * @return int
     * @Assert\Type(
     *     type="scalar",
     *     message="{{ value }}_is_not_{{ type }}"
     * )
     */
    public function isTypeScalar()
    {
        return $this->_t_get(1, array(1,2,3));
    }

    /**
     * @return string
     * @Assert\Type(
     *     type="string",
     *     message="{{ value }}_is_not_{{ type }}"
     * )
     */
    public function isTypeString()
    {
        return $this->_t_get('1', 1);
    }

    /**
     * @return string
     * @Assert\Choice(
     *     choices={"male", "female"},
     *     message="singleChoice_{{ value }}"
     * )
     */
    public function isValidSingleChoice()
    {
        return $this->_t_get('male', 'wrong_choice');
    }

    /**
     * @return array
     * @Assert\Choice(
     *     callback="getChoicesList",
     *     multiple=true,
     *     multipleMessage="multipleChoice_{{ value }}"
     * )
     */
    public function isValidMultipleChoice()
    {
        return $this->_t_get(array('June', 'July'), array('June', 'May', 'September'));
    }

    /**
     * @return array
     * @Assert\Choice(
     *     callback="getChoicesList",
     *     multiple=true,
     *     min=2,
     *     minMessage="minChoice_value_singular_{{ limit }}|minChoice_value_plural_{{ limit }}"
     * )
     */
    public function isMinMultipleChoice()
    {
        return $this->_t_get(array('June', 'July'), array('June'));
    }

    /**
     * @return array
     * @Assert\Choice(
     *     callback="getChoicesList",
     *     multiple=true,
     *     max=1,
     *     maxMessage="maxChoice_value_singular_{{ limit }}|maxChoice_value_plural_{{ limit }}"
     * )
     */
    public function isMaxMultipleChoice()
    {
        return $this->_t_get(array('June'), array('June', 'July'));
    }

    /**
     * @Assert\Callback
     * @param ExecutionContextInterface $context
     */
    public function validateCallback(ExecutionContextInterface $context)
    {
        if (!$this->isValid) {
            $context->buildViolation('callback_email_' . $this->getEmail())->atPath('email');
        }
    }
}
