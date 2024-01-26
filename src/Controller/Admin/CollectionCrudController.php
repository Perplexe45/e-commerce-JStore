<?php

namespace App\Controller\Admin;

use App\Entity\Collection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use phpDocumentor\Reflection\Types\Boolean;

class CollectionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Collection::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title'),
            TextField::new('description'),
            TextField::new('button_text'),
            TextField::new('button_link'),
            BooleanField::new('isMega'),
            ImageField::new('imageUrl')
            ->setBasePath("assets/images/collections")
            ->setUploadDir("/public/assets/images/collections")
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired($pageName === Crud::PAGE_NEW)
            ,
        ];
    }
   
}
