<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordEncoder
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * PasswordEncoder constructor.
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param $user
     * @param $plainPassword
     * @return string
     */
    public function hashPassword($user, $plainPassword)
    {
        return $this->encoder->encodePassword($user, $plainPassword);
    }
}
