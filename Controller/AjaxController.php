<?php
/**
 * Created by PhpStorm.
 * User: Yury Maltsev
 * Email: dev.ymalcev@gmail.com
 * Date: 11/28/13
 * Time: 11:57 AM
 */

namespace Fp\JsFormValidatorBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class AjaxController extends Controller {
    public function checkUniqueEntityAction()
    {
        $data = $this->getRequest()->request->all();
        foreach ($data['data'] as $value) {
            // If field(s) has an empty value and it should be ignored
            if ((bool)$data['ignoreNull'] && ('' === $value || is_null($value))) {
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