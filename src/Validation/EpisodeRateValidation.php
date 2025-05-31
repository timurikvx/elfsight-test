<?php

namespace App\Validation;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints;

class EpisodeRateValidation
{

    public function validate($data): void
    {
        $validator = Validation::createValidator();
        $constraints = new Collection([
            'text'=>[
                new Constraints\NotBlank([], 'Text review is empty'),
                new Constraints\Type('string', 'The review text must be a string')
            ],
            'id'=>[
                new Constraints\Type('integer'),
                new Constraints\GreaterThan(value: 0),
            ]
        ]);

        $errors = $validator->validate($data, $constraints);
        if($errors->count() === 0){
            return;
        }

        $errors_list = [];
        foreach ($errors as $error){
            $errors_list[$error->getPropertyPath()] = $error->getMessage();
        }

        $response = new JsonResponse(['validation_errors'=>$errors_list], 400);
        $response->send();
        exit();

    }

}
