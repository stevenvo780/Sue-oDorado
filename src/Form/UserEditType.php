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

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array('attr'  => array('class' => 'form-control form-control-lg')))
            ->add('nombre', TextType::class, array('attr'  => array('class' => 'form-control form-control-lg')))
            ->add('telfijo', TextType::class, array('attr'  => array('class' => 'form-control form-control-lg')))
            ->add('telmovil', TextType::class, array('attr'  => array('class' => 'form-control form-control-lg')))
            ->add('email', TextType::class, array('attr'  => array('class' => 'form-control form-control-lg')))
            ->add('edad', TextType::class, array('attr'  => array('class' => 'form-control form-control-lg')))
            ->add('referido', TextType::class, array('attr'  => array('class' => 'form-control form-control-lg')))
            ->add('pais', TextType::class, array('attr'  => array('class' => 'form-control form-control-lg')))
            ->add('ciudad', TextType::class, array('attr'  => array('class' => 'form-control form-control-lg')))
            ->add('monedasBitcoin', NumberType::class, array('attr'  => array('class' => 'form-control form-control-lg')))
            ->add('monedasMarketcoin', NumberType::class, array('attr'  => array('class' => 'form-control form-control-lg')))
            ->add('vecesRecividas', NumberType::class, array('attr'  => array('class' => 'form-control form-control-lg')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}
