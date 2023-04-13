<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class CategoryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = new User();
        $builder
            ->add('Name', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
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
            ->add('Validate', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
