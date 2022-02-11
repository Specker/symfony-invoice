<?php

namespace App\Form;

use App\Entity\InvoiceProduct;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddProductToInvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('product_id')
            ->add('quantity')
            ->add('save', SubmitType::class, ['label' => 'Add'])
            ->add('saveAndAdd', SubmitType::class, ['label' => 'Add Next'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InvoiceProduct::class,
        ]);
    }
}
