<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserTypeRegister extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, ['label' => false, 'attr' => ['placeholder' => "Correo", 'class' => 'form-control form-control-lg']])
            ->add('nombres', TextType::class, ['label' => false, 'attr' => ['placeholder' => "Nombre", 'class' => 'form-control form-control-lg']])
            ->add('apellidos', TextType::class, ['label' => false, 'attr' => ['placeholder' => "Apellidos", 'class' => 'form-control form-control-lg']])
            ->add('telmovil', TextType::class, ['label' => false, 'attr' => ['placeholder' => "Telefono movil", 'class' => 'form-control form-control-lg']])
            ->add('pais', TextType::class, ['label' => false, 'attr' => ['placeholder' => "Pais", 'class' => 'form-control form-control-lg']])
            ->add('ciudad', TextType::class, ['label' => false, 'attr' => ['placeholder' => "Ciudad", 'class' => 'form-control form-control-lg']])
            ->add('plainPassword', RepeatedType::class, array(
                'required' => true,
                'type' => PasswordType::class,
                'first_options' => array('label' => false, 'attr' => ['placeholder' => 'Contraseña', 'class' => 'form-control form-control-lg', 'autocomplete' => 'off']),
                'second_options' => array('label' => false, 'attr' => ['placeholder' => 'Repita contraseña', 'class' => 'form-control form-control-lg', 'autocomplete' => 'off']),
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
