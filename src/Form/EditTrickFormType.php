<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Media;
use App\Entity\Trick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditTrickFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a name',
                    ]),
                    new Length(['min' => 10]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a description',
                    ]),
                    new Length(['min' => 10]),
                ],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a category',
                    ]),
                ],
            ])
            ->add('media', FileType::class, [
                'multiple' => true,
                'label' => 'Profile image (JPG or PNG file)',
                'mapped' => false,
                'allow_extra_fields' => true,
                'required' => false,
                'row_attr' => ['class' => 'btn form-control bg-gray-50'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
            'constraints' => [
                new UniqueEntity([
                    'entityClass' => Trick::class,
                    'fields' => ['name'],
                    'message' => 'This trick already exists',
                ]),
            ]]);
    }

}