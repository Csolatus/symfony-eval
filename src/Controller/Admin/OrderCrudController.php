<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('reference'),
            TextField::new('status'),
            // Use virtual field for date to avoid Intl issues and TextField type errors
            TextField::new('formattedCreatedAt', 'Date de création'),
            TextField::new('formattedTotal', 'Total'),
            TextField::new('user.email', 'User'), // Simple text representation
        ];
    }
}
