<?php

namespace Nyholm\ClassRequirementExtractor\Test\Resources;

use Symfony\Component\Validator\Constraints as Assert;

class CreateCompany
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private $name;

    #[Assert\Sequentially([
        new Assert\NotBlank(),
        new Assert\Type('string'),
        new Assert\Uuid(),
    ])]
    private $client;

    #[Assert\NotNull]
    #[Assert\Type('array')]
    #[Assert\Unique]
    #[Assert\Count(min: 0, max: 100)]
    #[Assert\All([
        new Assert\Type('string'),
        new Assert\NotBlank(),
        new Assert\Type('digit'),
    ])]
    private $administrators = [];

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getClientId()
    {
        return $this->client;
    }

    public function setClient($client): void
    {
        $this->client = $client;
    }

    public function getAdministratorIds(): array
    {
        return $this->administrators;
    }

    public function setAdministrators(array $administrators): void
    {
        $this->administrators = $administrators;
    }
}
