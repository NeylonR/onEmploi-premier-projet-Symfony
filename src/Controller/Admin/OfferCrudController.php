<?php

namespace App\Controller\Admin;

use App\Entity\Offer;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use App\Entity\ContractType;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class OfferCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Offer::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            TextEditorField::new('description'),
            DateTimeField::new('createdAt')->onlyOnIndex(),
            AssociationField::new('contractType')->formatValue(function($value, $entity){
                return $entity->getContractType();
            }),
            AssociationField::new('author')->formatValue(function($value, $entity){
                return $entity->getAuthor();
            }),
            MoneyField::new('salary', 'Salaire mensuel')->setNumDecimals(2)->setCurrency('EUR'),
            NumberField::new('workingTime','Horaire total mensuel'),
            BooleanField::new('remote','Teletravail, coché = oui')
        ];
    }
    
}
