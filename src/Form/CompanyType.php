<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\LegalStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class ,['required' => false])
            ->add('name', TextType::class, ['required' => false])
            ->add('address', TextType::class)
            ->add('description', TextType::class, ['required' => false])
            ->add('country', TextType::class)
            ->add('city', TextType::class)
            ->add('siret', TextType::class)
            ->add('siren', TextType::class)
            ->add('tva', TextType::class)
            ->add('companyType', TextType::class)
            ->add('socialReason', TextType::class)
            ->add('type', TextType::class)
            ->add('capital', TextType::class)
            ->add('size', TextType::class)
            ->add('gelee', TextType::class)
            ->add('phone', TextType::class)
            ->add('siteAddress', TextType::class)
            ->add('addedBy', TextType::class)
            ->add('managerCivility', TextType::class)
            ->add('managerEmail', TextType::class)
            ->add('managerFirstName', TextType::class)
            ->add('managerLastName', TextType::class)
            ->add('image', FileType::class, [
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])
            ->add('calendar', CollectionType::class, [
                'entry_type'   => CalendarType::class,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Company::class,
        ));
    }
}
