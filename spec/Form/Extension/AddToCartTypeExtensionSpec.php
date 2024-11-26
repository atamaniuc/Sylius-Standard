<?php

namespace spec\App\Form\Extension;

use App\Form\Extension\AddToCartTypeExtension;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\Form\Type\CartItemType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Prophecy\Argument;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;
use Symfony\Component\Validator\ConstraintValidatorInterface;

class AddToCartTypeExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AddToCartTypeExtension::class);
    }

    function it_extends_abstract_type_extension()
    {
        $this->shouldBeAnInstanceOf(AbstractTypeExtension::class);
    }

    function it_extends_cart_item_type()
    {
        $this::getExtendedTypes()->shouldReturn([CartItemType::class]);
    }

    function it_modifies_quantity_field(FormBuilderInterface $builder)
    {
        $builder
            ->add('quantity', IntegerType::class, Argument::that(function($options) {
                if (!isset($options['attr']) ||
                    $options['attr']['min'] !== 10 ||
                    $options['attr']['step'] !== 10) {
                    return false;
                }

                if (!isset($options['data']) || $options['data'] !== 10) {
                    return false;
                }

                if (!isset($options['constraints']) ||
                    !($options['constraints'][0] instanceof Callback)) {
                    return false;
                }

                return true;
            }))
            ->shouldBeCalled()
            ->willReturn($builder);

        $this->buildForm($builder, []);
    }

    function it_validates_quantity_is_multiple_of_ten(
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $violationBuilder
    )
    {
        $context->buildViolation('Products can only be ordered in multiples of 10')
            ->willReturn($violationBuilder)->shouldBeCalledOnce();

        $violationBuilder->atPath('quantity')->willReturn($violationBuilder)->shouldBeCalledOnce();
        $violationBuilder->addViolation()->shouldBeCalledOnce();

        $callback = new Callback([
            'callback' => function ($value, ExecutionContextInterface $executionContext) {
                if ($value % 10 !== 0) {
                    $executionContext->buildViolation('Products can only be ordered in multiples of 10')
                        ->atPath('quantity')
                        ->addViolation();
                }
            }
        ]);

        ($callback->callback)(15, $context->getWrappedObject());
    }
}
