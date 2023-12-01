<?php

namespace Nyholm\ClassRequirementExtractor\Test\Resources;

use Symfony\Component\Validator\Constraints as Assert;

class CreateCampaign
{
    #[Assert\NotNull]
    #[Assert\Type('array')]
    #[Assert\Unique]
    #[Assert\Count(min: 1, max: 100)]
    #[Assert\All([
        new Assert\NotBlank(),
        new Assert\Type('string'),
        new Assert\Type('digit'),
    ])]
    private $users = [];

    /**
     * @return array<int, string>
     */
    public function getUserIds(): array
    {
        return $this->users;
    }

    /**
     * @param array<int, string> $users
     */
    public function setUsers(array $users): void
    {
        $this->users = $users;
    }
}
