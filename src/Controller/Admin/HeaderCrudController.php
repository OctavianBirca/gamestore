<?php

namespace App\Controller\Admin;

use App\Entity\Header;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class HeaderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Header::class;
    }

    
    public function configureFields(string $pageName): iterable
    {   
        $imageRequierd = true;
            if($pageName == 'edit') {
                $imageRequierd = false; 
            }

        return [
        
            TextField::new('title', 'Titre'),
            TextareaField::new('content', 'Contenu'),
            TextField::new('buttonTitle', 'Titre de button'),
            TextField::new('buttonLink', 'URL de button'),
            ImageField::new('image')
                ->setLabel('Background')
                ->setHelp("L'image doit avoir maximum 600x600 px" )
                ->setBasePath('uploads/')
                ->setUploadDir('/public/uploads')
                ->setRequired($imageRequierd),
        ];
    }
    
}
