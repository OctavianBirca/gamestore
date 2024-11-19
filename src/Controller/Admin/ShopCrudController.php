<?php

namespace App\Controller\Admin;

use App\Entity\Shop;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ShopCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Shop::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            TextField::new('address')->setLabel("Adresse"),
            TextField::new('postalCode')->setLabel("Code Postal"),
            TextField::new('city')->setLabel("Ville"), 
            TextField::new('slug')->setLabel("slug"),
            ImageField::new('image')
                ->setLabel('Image')
                ->setHelp("L'image doit avoir maximum 600x600 px" )
                ->setBasePath('uploads/shops')
                ->setUploadDir('/public/uploads/shops'),
            TextEditorField::new('description')->setLabel("Description"),
            TextEditorField::new('googleMaps')->setLabel("Google Maps"),

        ];
    }
   
}
