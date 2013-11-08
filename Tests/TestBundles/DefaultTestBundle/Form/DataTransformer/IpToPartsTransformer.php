<?php
namespace Fp\JsFormValidatorBundle\Tests\TestBundles\DefaultTestBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class IpToPartsTransformer implements DataTransformerInterface
{
    /**
     * Transforms a value from the original representation to a transformed representation.
     *
     * @param mixed $value
     *
     * @return mixed|string
     */
    public function transform($value)
    {
        if (null === $value) {
            return '';
        }

        return implode(':', $value);
    }

    /**
     * Transforms a value from the transformed representation to its original
     * representation.
     *
     * @param mixed $ip
     *
     * @return array|mixed|null
     */
    public function reverseTransform($ip)
    {
        if (empty($ip)) {
            return null;
        }

        return explode(':', $ip);
    }
}
