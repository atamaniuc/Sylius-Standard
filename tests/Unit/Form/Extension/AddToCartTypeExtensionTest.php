<?php

namespace App\Tests\Unit\Form\Extension;

use App\Form\Extension\AddToCartTypeExtension;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\OrderBundle\Form\Type\CartItemType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Callback;

class AddToCartTypeExtensionTest extends TestCase
{
    public function testItExtendsCartItemType(): void
    {
        $extension = new AddToCartTypeExtension();
        $this->assertContains(CartItemType::class, $extension::getExtendedTypes());
    }

    public function testItModifiesQuantityField(): void
    {
        $formBuilder = $this->createMock(FormBuilderInterface::class);
        $formBuilder
            ->expects($this->once())
            ->method('add')
            ->with(
                'quantity',
                IntegerType::class,
                $this->callback(function ($options) {
                    return is_array($options)
                        && isset(
                            $options['attr']['min'],
                            $options['attr']['step'],
                            $options['data'],
                            $options['constraints'],
                        )
                        && $options['attr']['min'] === 10
                        && $options['attr']['step'] === 10
                        && $options['data'] === 10
                        && is_array($options['constraints'])
                        && count($options['constraints']) === 1
                        && $options['constraints'][0] instanceof Callback;
                }),
            );

        $extension = new AddToCartTypeExtension();
        $extension->buildForm($formBuilder, []);
    }
}
