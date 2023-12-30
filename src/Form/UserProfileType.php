<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\DateType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use function Zenstruck\Foundry\faker;

class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('firstName')
            ->add('lastName')
            ->add('bio', TextareaType::class, [
                'required' => false,
            ])
            ->add('websiteUrl')
            ->add('location', CountryType::class)
            ->add('dateOfBirth', BirthdayType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('email', EmailType::class)
            ->add('imageName', FileType::class, [
                'allow_extra_fields' => true,
                'label' => 'Profile picture',
                'required' => false,
                'row_attr' => ['class' => 'btn form-control bg-gray-50'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'constraints' => [
                new UniqueEntity([
                    'entityClass' => User::class,
                    'fields' => ['username'],
                    'message' => 'This username is already taken',
                ]),
            ]
        ]);
    }
}
