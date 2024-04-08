<?php

use Symfony\Component\Validator\ConstraintViolationListInterface;

function createErrorPayload(ConstraintViolationListInterface $violations): array
{
    foreach ($violations as $violation) {
        $errors[$violation->getPropertyPath()][] = $violation->getMessage();
    }

    return $errors;
}
