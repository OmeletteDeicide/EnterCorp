<?php

namespace App\Form;

use App\Entity\Board;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BoardFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name')
        ->add('roles_options', ChoiceType::class, array(
            "mapped" =>  false,
            "multiple" => true,
            "expanded" => true,
            'choices'  => array(
                'Insider' => 'ROLE_INSIDER',
                'Collaborator' => 'ROLE_COLLABORATOR',
                'External' => 'ROLE_EXTERNAL',
            )
        ))
        ->add('validation', SubmitType::class)
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Board::class,
        ]);
    }
}
