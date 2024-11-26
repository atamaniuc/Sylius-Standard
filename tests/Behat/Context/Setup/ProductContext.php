<?php

declare(strict_types=1);

namespace App\Tests\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Product\Factory\ProductFactoryInterface;
use Sylius\Component\Product\Factory\ProductVariantFactoryInterface;
use Sylius\Component\Product\Model\ProductTranslationInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class ProductContext implements Context
{
    public function __construct(
        private ProductFactoryInterface $productFactory,
        private ProductVariantFactoryInterface $productVariantFactory,
        private ProductRepositoryInterface $productRepository,
        private FactoryInterface $productTranslationFactory,
        private FactoryInterface $channelPricingFactory,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @Given the store has a product :productName priced at :price
     */
    public function theStoreHasAProductPricedAt(string $productName, int $price): void
    {
        /** @var ProductInterface $product */
        $product = $this->productFactory->createNew();

        /** @var ProductTranslationInterface $translation */
        $translation = $this->productTranslationFactory->createNew();
        $translation->setLocale('en_US');
        $translation->setName($productName);
        $translation->setSlug($this->createSlug($productName));

        $product->addTranslation($translation);
        $product->setCode(strtoupper(str_replace(' ', '_', $productName)));

        $variant = $this->productVariantFactory->createNew();
        $variant->setProduct($product);
        $variant->setCode(strtoupper(str_replace(' ', '_', $productName)) . '_VARIANT');
        $variant->setOnHand(10);

        /** @var ChannelPricingInterface $channelPricing */
        $channelPricing = $this->channelPricingFactory->createNew();
        $channelPricing->setChannelCode('FASHION_WEB');
        $channelPricing->setPrice($price);
        $variant->addChannelPricing($channelPricing);

        $product->addVariant($variant);

        $this->productRepository->add($product);
        $this->entityManager->flush();
    }

    private function createSlug(string $name): string
    {
        return strtolower(str_replace(' ', '-', trim($name)));
    }
}
