<?php

declare(strict_types=1);

namespace App\Form\Extension;

use Sylius\Bundle\OrderBundle\Form\Type\CartItemType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class AddToCartTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [CartItemType::class];
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('quantity', IntegerType::class, [
            'attr' => [
                // TODO: Get `10` from const (and also pass it to quantity-validation.js too as well)
                'min' => 10,
                'step' => 10,
            ],
            'data' => 10, // Set default value
            'constraints' => [
                new Callback(function ($value, ExecutionContextInterface $context) {
                    if ($value % 10 !== 0) {
                        $context->buildViolation('Products can only be ordered in multiples of 10')
                            ->atPath('quantity')
                            ->addViolation();
                    }
                }),
            ],
        ]);
    }
}
