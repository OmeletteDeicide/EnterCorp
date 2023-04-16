<?php

namespace App\Form;

use App\Entity\Subject;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubjectFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            // ->add('creationDate')
            ->add(
                'roles_options', ChoiceType::class,
                array(
                    "mapped" => false,
                    "multiple" => true,
                    "expanded" => true,
                    'choices' => array(
                        'Insider' => 'ROLE_INSIDER',
                        'Collaborator' => 'ROLE_COLLABORATOR',
                        'External' => 'ROLE_EXTERNAL',
                    )
                )
            )
            // ->add('User')
            // ->add('Board')
            ->add('validate', SubmitType::class)
        ;
    }
<?php

namespace App\Form;

use App\Entity\Subject;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubjectFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            // ->add('creationDate')
            ->add(
                'roles_options', ChoiceType::class,
                array(
                    "mapped" => false,
                    "multiple" => true,
                    "expanded" => true,
                    'choices' => array(
                        'Insider' => 'ROLE_INSIDER',
                        'Collaborator' => 'ROLE_COLLABORATOR',
                        'External' => 'ROLE_EXTERNAL',
                    )
                )
            )
            // ->add('User')
            // ->add('Board')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subject::class,
        ]);
    }
}