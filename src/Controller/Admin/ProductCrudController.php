<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Produit')
            ->setEntityLabelInPlural('Produits')
            
            
        ;
    }


    
    public function configureFields(string $pageName): iterable
    {
        $imageRequierd = true;
            if($pageName == 'edit') {
                $imageRequierd = false; 
            }

        return [
            
            TextField::new('name')
                ->setLabel('Titre')
                ->setHelp('Titre du produits'),
            BooleanField::new('homepage')
                ->setLabel("Accueil"),
            SlugField::new('slug')
                ->setLabel('slug - url')
                ->setTargetFieldName('name')
                ->setHelp('URL de votre produits'),
            TextEditorField::new('description')
                ->setLabel('Description'),
            ImageField::new('image')
                ->setLabel('Image')
                ->setHelp("L'image doit avoir maximum 600x600 px" )
                ->setBasePath('uploads/')
                ->setUploadDir('/public/uploads')
                ->setRequired($imageRequierd),
            NumberField::new('price')->setLabel('Prix')->setHelp('Prix en Euro'),
            AssociationField::new('categories')
                ->setLabel('Catégories')
                ->setHelp('Sélectionnez une ou plusieurs catégories')
                ->setFormTypeOptions([
                    'by_reference' => false,
                    'multiple' => true,
            ]),
            ChoiceField::new('pegi')
                ->setLabel('PEGI')
                ->setChoices([
                    '3' => 3,
                    '7' => 7,
                    '12' => 12,
                    '16' => 16,
                    '18' => 18,
            ])
                ->setHelp('Sélectionnez le PEGI approprié pour le produit'),
            NumberField::new('stock')->setLabel('Stock')
        ];

            
    }
    
}
