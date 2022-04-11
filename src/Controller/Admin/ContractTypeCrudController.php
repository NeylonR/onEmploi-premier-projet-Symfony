<?php

namespace App\Controller\Admin;

use App\Entity\ContractType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ContractTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ContractType::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
