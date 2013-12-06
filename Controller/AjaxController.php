<?php

namespace Fp\JsFormValidatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * These actions call from the client side to check some validations on the server side
 * Class AjaxController
 *
 * @package Fp\JsFormValidatorBundle\Controller
 */
class AjaxController extends Controller
{
    /**
     * This is simplified analog for the UniqueEntity validator
     * @return JsonResponse
     */
    public function checkUniqueEntityAction()
    {
        $data = $this->getRequest()->request->all();
        foreach ($data['data'] as $value) {
            // If field(s) has an empty value and it should be ignored
            if ((bool) $data['ignoreNull'] && ('' === $value || is_null($value))) {
                // Just return a positive result
                return new JsonResponse(true);
            }
        }

        $entity = $this
            ->get('doctrine')
            ->getRepository($data['entity'])
            ->{$data['repositoryMethod']}($data['data'])
        ;

        return new JsonResponse(empty($entity));
    }
} 