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
            ->add('email', EmailType::class, ['label' => false, 'attr'  => ['placeholder' => "Correo", 'class' => 'form-control form-control-lg']])
            ->add('nombres', TextType::class, ['label' => false, 'attr'  => ['placeholder' => "Nombre", 'class' => 'form-control form-control-lg']])
            ->add('apellidos', TextType::class, ['label' => false, 'attr'  => ['placeholder' => "Apellidos", 'class' => 'form-control form-control-lg']])
            ->add('telmovil', TextType::class, ['label' => false, 'attr'  => ['placeholder' => "Telefono movil", 'class' => 'form-control form-control-lg']])
            ->add('pais', TextType::class, ['label' => false, 'attr'  => ['placeholder' => "Pais",'class' => 'form-control form-control-lg']])
            ->add('ciudad', TextType::class, ['label' => false, 'attr'  => ['placeholder' => "Ciudad", 'class' => 'form-control form-control-lg']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}
