<?php
namespace Fenchy\MessageBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Fenchy\UserBundle\Entity\User;

class UserToIdTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms a user object to a integer number (id).
     *
     * @param  User|null $user
     * @return integer
     */
    public function transform($user)
    {
        if (null === $user) {
            return null;
        }

        return $user->getId();
    }

    /**
     * Transforms a integer number (id) to a user object.
     *
     * @param  string $id
     * @return User|null
     * @throws TransformationFailedException if user object is not found.
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }

        $user = $this->om
            ->getRepository('UserBundle:User')
            ->findOneById($id);

        if (null === $user) {
            throw new TransformationFailedException(sprintf(
                'User with id "%s" does not exist!',
                $id
            ));
        }

        return $user;
    }
}