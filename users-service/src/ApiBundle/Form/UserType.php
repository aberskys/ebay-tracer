<?php

namespace ApiBundle\Form;

use ApiBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('plainPassword', PasswordType::class)
            ->add('addressLine1', TextType::class)
            ->add('addressLine2', TextType::class)
            ->add('zipCode', TextType::class)
            ->add('city', TextType::class)
            ->add('country', TextType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ));
    }

    /**
     * @return string
     */
    public function setBlockPrefix(): string
    {
        return 'user';
    }
}