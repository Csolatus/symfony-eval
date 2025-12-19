<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // 1. Create Categories
        $categories = [];
        $categoryNames = ['Mode', 'Maison', 'Technologie'];

        foreach ($categoryNames as $name) {
            $category = new Category();
            $category->setName($name);
            $category->setSlug(strtolower($name));
            $manager->persist($category);
            $categories[] = $category;
        }

        // 2. Create Products
        $productsData = [
            ['name' => 'T-Shirt Premium', 'price' => 29.00, 'category' => $categories[0], 'description' => '<p>Un t-shirt en coton bio d\'une douceur incomparable.</p>'],
            ['name' => 'Jean Slim', 'price' => 59.00, 'category' => $categories[0], 'description' => '<p>Coupe parfaite, denim japonais.</p>'],
            ['name' => 'Lampe Design', 'price' => 120.00, 'category' => $categories[1], 'description' => '<p>Éclairage d\'ambiance pour votre salon.</p>'],
            ['name' => 'Casque Audio', 'price' => 199.00, 'category' => $categories[2], 'description' => '<p>Son haute fidélité, réduction de bruit active.</p>'],
        ];

        foreach ($productsData as $data) {
            $product = new Product();
            $product->setName($data['name']);
            $product->setPrice($data['price']);
            $product->setCategory($data['category']);
            $product->setDescription($data['description']);
            $manager->persist($product);
        }

        // 3. Create Admin User
        $user = new User();
        $user->setEmail('admin@sfcommerce.com');
        $user->setFullName('Admin User');
        $user->setAddress('123 Rue de Symfony');
        $user->setCity('Paris');
        $user->setPostalCode('75001');
        $user->setRoles(['ROLE_ADMIN']);
        $password = $this->hasher->hashPassword($user, 'password');
        $user->setPassword($password);
        $manager->persist($user);

        $manager->flush();
    }
}
