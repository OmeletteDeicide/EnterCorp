<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    
    public function configureFields(string $pageName): iterable
    {

        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('Pseudo'),
            TextField::new('Email'),
            TextField::new('password'),
            ChoiceField::new('roles')
            ->setChoices([
                'Administrateur' =>'ROLE_ADMIN' ,
                'Insider' =>'ROLE_INSIDER',
                'Collaborator'=> 'ROLE_COLLABORATOR' ,
                'External' => 'ROLE_EXTERNAL',
            ])
            ->allowMultipleChoices(),
            BooleanField::new("Blocked"),
        ];
    }
    
}
