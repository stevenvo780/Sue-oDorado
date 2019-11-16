<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array('attr'  => array('class' => 'form-control form-control-lg')))
            ->add('nombre', TextType::class, array('attr'  => array('class' => 'form-control form-control-lg')))
            ->add('telfijo', TextType::class, array('attr'  => array('class' => 'form-control form-control-lg')))
            ->add('telmovil', TextType::class, array('attr'  => array('class' => 'form-control form-control-lg')))
            ->add('email', TextType::class, array('attr'  => array('class' => 'form-control form-control-lg')))
            ->add('referido', TextType::class, array('attr'  => array('class' => 'form-control form-control-lg')))
            ->add('pais', NumberType::class, array('attr'  => array('class' => 'form-control form-control-lg')))
            ->add('ciudad', NumberType::class, array('attr'  => array('class' => 'form-control form-control-lg')))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
                'options' => array('attr' => array('class' => 'form-control form-control-lg')),
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}
