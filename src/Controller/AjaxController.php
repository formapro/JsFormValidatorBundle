<?php

namespace Fp\JsFormValidatorBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * These actions call from the client side to check some validations on the server side
 * Class AjaxController
 *
 * @package Fp\JsFormValidatorBundle\Controller
 */
class AjaxController
{
    /**
     * @var ManagerRegistry|null
     */
    private $doctrine;

    /**
     * @param ManagerRegistry|null $doctrine
     */
    public function __construct(?ManagerRegistry $doctrine = null)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * This is simplified analog for the UniqueEntity validator
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return JsonResponse
     */
    public function checkUniqueEntityAction(Request $request)
    {
        if (!$this->doctrine) {
            throw new \LogicException('Doctrine is required to use the UniqueEntity JavaScript validator endpoint.');
        }

        $data = $request->request->all();
        if (!array_key_exists('data', $data) || !is_array($data['data'])) {
            throw new BadRequestHttpException('The "data" request field is required.');
        }

        $values = $data['data'];
        $ignoreNull = !empty($data['ignoreNull']);

        foreach ($values as $value) {
            // If field(s) has an empty value and it should be ignored
            if ($ignoreNull && ('' === $value || is_null($value))) {
                // Just return a positive result
                return new JsonResponse(true);
            }
        }

        $entity = $this->doctrine
            ->getRepository($data['entityName'])
            ->{$data['repositoryMethod']}($values)
        ;

        return new JsonResponse(empty($entity));
    }
}
