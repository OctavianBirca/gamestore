<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use PhpParser\Node\Stmt\Label;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserCrudController extends AbstractCrudController
{
    
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
           // ->setDateFormat('...')
            // ...
        ;
    }


    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new ('firstname')->setLabel('Prenom'),
            TextField::new ('lastname')->setLabel('Nom'),
            EmailField::new('email', 'Email')->setRequired(true),
            ChoiceField::new('roles')->setChoices([
                'ROLE_USER' => 'ROLE_USER',
                'ROLE_ADMIN' => 'ROLE_ADMIN',
                'ROLE_EMPLOYEE' => 'ROLE_EMPLOYEE',
            ])->allowMultipleChoices(),
            TextField::new('plainPassword')
                ->setLabel('Parola')
                ->setRequired($pageName === 'new') // Este necesară la creare, dar nu la editare
                ->onlyOnForms()// Afișează câmpul doar în formulare, nu și în listele de afișare
            ]
            ;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof User) {
            return;
        }

        // Criptăm parola numai dacă este setată parola brută
        if ($plainPassword = $entityInstance->getPlainPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword($entityInstance, $plainPassword);
            $entityInstance->setPassword($hashedPassword);
            $entityInstance->setPlainPassword(null); //
        }

        parent::persistEntity($entityManager, $entityInstance);
    }




}    