<?php

namespace Fp\JsFormValidatorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return JsonResponse
     */
    public function checkUniqueEntityAction(Request $request)
    {
        $data = $request->request->all();
        foreach ($data['data'] as $value) {
            // If field(s) has an empty value and it should be ignored
            if ((bool) $data['ignoreNull'] && ('' === $value || is_null($value))) {
                // Just return a positive result
                return new JsonResponse(true);
            }
        }

        $entity = $this
            ->get('doctrine')
            ->getRepository($data['entityName'])
            ->{$data['repositoryMethod']}($data['data'])
        ;

        return new JsonResponse(empty($entity));
    }
} 