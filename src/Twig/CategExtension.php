<?php

namespace App\Twig;

use App\Entity\Categories;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CategExtension extends AbstractExtension
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function twigCustomFunction(): array
    {
        return [
            new TwigFunction('categ', [$this, 'getCategories'])
        ];
    }

    public function getCategories(): array
    {
        return $this->em->getRepository(Categories::class)->findBy([], ['name' => 'ASC']);
    }
}