<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Todo;
use App\Tools\Constants;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Categories
        $categories = $this->loadCategories($manager);

        // Todos
        foreach($categories as $category) {
            $todo = new Todo();
            $todo->setTitle('Titre de : ' . $category->getName());
            $todo->setContent('Contenu de : ' . $category->getName());
            $todo->setCategory($category);

            $manager->persist($todo);
        }

        $manager->flush();
    }

    private function loadCategories(ObjectManager $manager): array
    {
        $output = [];

        foreach(Constants::DEFAULT_CATEGORIES as $catName) {
            $category = new Category();
            $category->setName($catName);

            $manager->persist($category);
            $output[] = $category;
        }

        $manager->flush();

        return $output;
    }
}
