<?php

namespace ApiBundle\Form;

use ApiBundle\Entity\SaleOrder;
use ApiBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SaleOrderType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('buyerId', IntegerType::class)
            ->add('sellerId', IntegerType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => SaleOrder::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ));
    }

    /**
     * @return string
     */
    public function setBlockPrefix(): string
    {
        return 'order';
    }
}