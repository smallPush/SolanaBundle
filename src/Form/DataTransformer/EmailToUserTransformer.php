<?php

namespace App\Form\DataTransformer;

use App\Application\User\Query\GetUserByEmailQuery;
use App\Contract\Bus\QueryBusInterface;
use App\Entity\User;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EmailToUserTransformer implements DataTransformerInterface
{
    public function __construct(
        private QueryBusInterface $queryBus,
    ) {
    }

    /**
     * Transforms an object (user) to a string (email).
     *
     * @param  User|null $user
     */
    public function transform($user): string
    {
        if (null === $user) {
            return '';
        }

        return $user->getEmail();
    }

    /**
     * Transforms a string (email) to an object (user).
     *
     * @param  string $email
     * @throws TransformationFailedException if object (user) is not found.
     */
    public function reverseTransform($email): ?User
    {
        // no email? It's optional, so that's ok
        if (!$email) {
            return null;
        }

        $user = $this->queryBus->ask(new GetUserByEmailQuery($email));

        if (null === $user) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An user with email "%s" does not exist!',
                $email
            ));
        }

        return $user;
    }
}
