<?php

namespace App\Validation;

use App\Entity\Episode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validation;

class EpisodeRatingValidation
{

    public function __construct(private EntityManagerInterface $entityManager)
    {

    }

    public function validateRating($data): void
    {
        $validator = Validation::createValidator();
        $constraints = new Collection([
            'text'=>[
                new Constraints\NotBlank(message: 'Text review is empty'),
                new Constraints\Type('string', message: 'The review text must be a string')
            ],
            'id'=>[
                new Constraints\Type('integer'),
                new Constraints\GreaterThan(value: 0),
            ]
        ]);

        $errors = $validator->validate($data, $constraints);
        $this->sendErrors($errors);

        $id = $data['id'];
        $episode = $this->entityManager->getRepository(Episode::class)->findOneBy(['api_id'=>$id]);

        $constraints = new Collection([
            'id'=>[
                new Constraints\NotNull(message: 'Episode with ID '.$id.' not found'),
            ]
        ]);

        $errors = $validator->validate(['id'=>$episode], $constraints);
        $this->sendErrors($errors, false);
    }

    public function validateSummary($id): void
    {
        $validator = Validation::createValidator();
        $episode = $this->entityManager->getRepository(Episode::class)->findOneBy(['api_id'=>$id]);

        $constraints = new Collection([
            'id'=>[
                new Constraints\NotNull(message: 'Episode with ID '.$id.' not found'),
            ]
        ]);

        $errors = $validator->validate(['id'=>$episode], $constraints);
        $this->sendErrors($errors, false);
    }

    private function sendErrors($errors, $withProperty = true): void
    {
        if($errors->count() === 0){
            return;
        }

        $errors_list = [];
        foreach ($errors as $error){
            $property = $error->getPropertyPath();
            if($withProperty){
                $errors_list[$property] = $error->getMessage();
            }else{
                $errors_list[] = $error->getMessage();
            }

        }

        $response = new JsonResponse(['validation_errors'=>$errors_list], 500);
        $response->send();
        exit();
    }

}
