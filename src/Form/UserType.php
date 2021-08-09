<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\StringType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Unique;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Unique()
                ]
            ])
            ->add('name', StringType::class, ['constraints' => new NotBlank()] )
            ->add('surname', StringType::class, ['constraints' => new NotBlank()])
            ->add('phone', IntegerType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Regex("/^[1-9].[0-9]{8}/")
                ]
            ])
            ->add('birthday')
        ;
        $builder
            ->add('alerts', CollectionType::class, [
                'entry_type' => AlertType::class,
                'entry_options' => ['label' => false],
                'by_reference' => false,
                'allow_add' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
