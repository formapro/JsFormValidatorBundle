<?php

namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Book
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Entity\BasicConstraintsRepository")
 *
 * @UniqueEntity(
 *     fields={"email"},
 *     message="unique_{{ value }}"
 * )
 */
class BasicConstraintsEntity
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
     * @Assert\Blank(message="blank_{{ value }}")
     */
    private $blank;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="not_blank_{{ value }}")
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
     *     pattern="/^a{3}$/"
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
     * @Assert\DateTime(message="datetime_{{ value }}")
     */
    private $datetime;

    public function populate($data)
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
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
     * @Assert\True(message="true_{{ value }}")
     */
    public function isTrue()
    {
        return true;
    }

    /**
     * @return bool
     * @Assert\False(message="false_{{ value }}")
     */
    public function isFalse()
    {
        return false;
    }

    /**
     * @return bool
     * @Assert\Null(message="null_{{ value }}")
     */
    public function isNull()
    {
        return null;
    }

    /**
     * @return bool
     * @Assert\NotNull(message="not_null_{{ value }}")
     */
    public function isNotNull()
    {
        return 1;
    }

    /**
     * @Assert\EqualTo(message="{{ value }}_equalTo_{{ compared_value }}", value=1)
     */
    public function isEqualTo()
    {
        return 1;
    }

    /**
     * @Assert\NotEqualTo(message="{{ value }}_notEqualTo_{{ compared_value }}", value=1)
     */
    public function isNotEqualTo()
    {
        return 1;
    }

    /**
     * @Assert\IdenticalTo(message="{{ value }}_identicalTo_{{ compared_value }}", value=1)
     */
    public function isIdenticalTo()
    {
        return 1;
    }

    /**
     * @Assert\NotIdenticalTo(message="{{ value }}_notIdenticalTo_{{ compared_value }}", value=1)
     */
    public function isNotIdenticalTo()
    {
        return 1;
    }

    /**
     * @Assert\LessThan(message="{{ value }}_lessThan_{{ compared_value }}", value=1)
     */
    public function isLessThan()
    {
        return 1;
    }

    /**
     * @Assert\LessThanOrEqual(message="{{ value }}_lessThanOrEqual_{{ compared_value }}", value=1)
     */
    public function isLessThanOrEqual()
    {
        return 1;
    }

    /**
     * @Assert\GreaterThan(message="{{ value }}_greaterThan_{{ compared_value }}", value=1)
     */
    public function isGreaterThan()
    {
        return 1;
    }

    /**
     * @Assert\GreaterThanOrEqual(message="{{ value }}_greaterThanOrEqual_{{ compared_value }}", value=1)
     */
    public function isGreaterThanOrEqual()
    {
        return 1;
    }

    /**
     * @Assert\Length(
     *     min=1,
     *     minMessage="{{ value }}_minLength_{{ limit }}"
     * )
     */
    public function isLengthMin()
    {
        return 'a';
    }

    /**
     * @Assert\Length(
     *     max=1,
     *     maxMessage="{{ value }}_maxLength_{{ limit }}"
     * )
     */
    public function isLengthMax()
    {
        return 'a';
    }

    /**
     * @Assert\Length(
     *     min=1,
     *     max=1,
     *     exactMessage="{{ value }}_exactLength_{{ limit }}"
     * )
     */
    public function isLengthExact()
    {
        return 'a';
    }

    /**
     * @Assert\Count(
     *     min=1,
     *     minMessage="{{ value }}_minCount_{{ limit }}"
     * )
     */
    public function isCountMin()
    {
        return array('a');
    }

    /**
     * @Assert\Count(
     *     max=1,
     *     maxMessage="{{ value }}_maxCount_{{ limit }}"
     * )
     */
    public function isCountMax()
    {
        return array('a');
    }

    /**
     * @Assert\Count(
     *     min=1,
     *     max=1,
     *     exactMessage="{{ value }}_exactCount_{{ limit }}"
     * )
     */
    public function isCountExact()
    {
        return array('a');
    }

    /**
     * @Assert\Range(
     *     min=1,
     *     minMessage="{{ value }}_minRange_{{ limit }}"
     * )
     */
    public function isRangeMin()
    {
        return 1;
    }

    /**
     * @Assert\Range(
     *     max=1,
     *     maxMessage="{{ value }}_maxRange_{{ limit }}"
     * )
     */
    public function isRangeMax()
    {
        return 1;
    }

    /**
     * @Assert\Range(
     *     min=1,
     *     min=1,
     *     invalidMessage="{{ value }}_invalidRangeValue"
     * )
     */
    public function isRangeValueValid()
    {
        return 1;
    }

    /**
     * @Assert\Type(
     *     type="array",
     *     message="{{ value }}_is_not_{{ type }}"
     * )
     */
    public function isTypeArray()
    {
        return array();
    }

    /**
     * @Assert\Type(
     *     type="boolean",
     *     message="{{ value }}_is_not_{{ type }}"
     * )
     */
    public function isTypeBool()
    {
        return true;
    }

    /**
     * @Assert\Type(
     *     type="callable",
     *     message="{{ value }}_is_not_{{ type }}"
     * )
     */
    public function isTypeCallable()
    {
        return function(){};
    }

    /**
     * @Assert\Type(
     *     type="float",
     *     message="{{ value }}_is_not_{{ type }}"
     * )
     */
    public function isTypeFloat()
    {
        return 1.5;
    }

    /**
     * @Assert\Type(
     *     type="integer",
     *     message="{{ value }}_is_not_{{ type }}"
     * )
     */
    public function isTypeInteger()
    {
        return 1;
    }

    /**
     * @Assert\Type(
     *     type="null",
     *     message="{{ value }}_is_not_{{ type }}"
     * )
     */
    public function isTypeNull()
    {
        return null;
    }

    /**
     * @Assert\Type(
     *     type="numeric",
     *     message="{{ value }}_is_not_{{ type }}"
     * )
     */
    public function isTypeNumeric()
    {
        return 1;
    }

    /**
     * @Assert\Type(
     *     type="object",
     *     message="{{ value }}_is_not_{{ type }}"
     * )
     */
    public function isTypeObject()
    {
        return new \StdClass();
    }

    /**
     * @Assert\Type(
     *     type="scalar",
     *     message="{{ value }}_is_not_{{ type }}"
     * )
     */
    public function isTypeScalar()
    {
        return 1;
    }

    /**
     * @Assert\Type(
     *     type="string",
     *     message="{{ value }}_is_not_{{ type }}"
     * )
     */
    public function isTypeString()
    {
        return 'a';
    }

    public static function getChoicesList()
    {
        return array('June', 'July', 'August');
    }


    /**
     * @Assert\Choice(
     *     choices={"male", "female"},
     *     message="singleChoice_{{ value }}"
     * )
     */
    public function isValidSingleChoice()
    {
        return 'male';
    }

    /**
     * @Assert\Choice(
     *     callback="getChoicesList",
     *     multiple=true,
     *     multipleMessage="multipleChoice_{{ value }}"
     * )
     */
    public function isValidMultipleChoice()
    {
        return array('June', 'July');
    }

    /**
     * @Assert\Choice(
     *     callback="getChoicesList",
     *     multiple=true,
     *     min=2,
     *     minMessage="minChoice_{{ value }}"
     * )
     */
    public function isMinMultipleChoice()
    {
        return array('June', 'July');
    }

    /**
     * @Assert\Choice(
     *     callback="getChoicesList",
     *     multiple=true,
     *     max=1,
     *     maxMessage="maxChoice_{{ value }}"
     * )
     */
    public function isMaxMultipleChoice()
    {
        return array('June', 'July');
    }
}
