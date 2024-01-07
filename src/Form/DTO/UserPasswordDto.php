<?php

namespace App\Form\DTO;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

class UserPasswordDto
{
    #[SecurityAssert\UserPassword(
        message: 'Wrong value for your current password',
    )]
    protected string $oldPassword;
    #[Assert\Length(
        min: 2,
        minMessage: 'Your password must be at least {{ limit }} characters long',
    )]
    public ?string $newPassword = null;
    #[Assert\IdenticalTo(
        propertyPath: 'newPassword',
        message: 'The password confirmation does not match',
    )]
    public ?string $confirmPassword = null;

    public function getOldPassword(): string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(?string $newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(?string $confirmPassword): void
    {
        $this->confirmPassword = $confirmPassword;
    }
}