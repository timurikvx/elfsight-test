<?php

namespace App\Validation\Rules;

use App\Entity\Episode;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class EpisodeExist extends Constraint
{
    public string $message = 'The entered episode was not found';

    public string $entity = Episode::class;

    public string $field = 'api_id';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

}
