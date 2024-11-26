<?php

declare(strict_types=1);

namespace App\Tests\Behat\Context\Shop;

use App\Tests\Behat\Page\Shop\Product\ShowPage;
use App\Tests\Behat\Trait\PantherTrait;
use Behat\Behat\Context\Context;
use Webmozart\Assert\Assert;

final class CartContext implements Context
{
    use PantherTrait;

    public function __construct(
        readonly private ShowPage $productShowPage
    ) {
    }

    /**
     * @Given I am on the product page for :productName
     */
    public function iAmOnTheProductPageFor(string $productName): void
    {
        $this->productShowPage->open(['slug' => $this->getProductSlug($productName)]);
    }

    /**
     * @When I set quantity to :quantity
     */
    public function iSetQuantityTo(int $quantity): void
    {
        $this->productShowPage->specifyQuantity($quantity);
    }

    /**
     * @When I add it to the cart
     * @When I try to add it to the cart
     */
    public function iAddItToTheCart(): void
    {
        $this->productShowPage->addToCart();
    }

    /**
     * @Then I should be notified that products can only be ordered in multiples of :multiple
     */
    public function iShouldBeNotifiedThatProductsCanOnlyBeOrderedInMultiplesOf(int $multiple): void
    {
        Assert::same(
            $this->productShowPage->getValidationMessage(),
            sprintf('Products can only be ordered in multiples of %d', $multiple)
        );
    }

    /**
     * @Then I should see :message message
     */
    public function iShouldSeeMessage(string $message): void
    {
//        $alertText = $this->productShowPage->getAlertText();
//        Assert::same($alertText, $message);
        $client = self::createPantherClient();
        $alertText = $client->switchTo()->alert()->getText();
        Assert::same($alertText, $message);
    }

    private function getProductSlug(string $productName): string
    {
        return strtolower(str_replace(' ', '-', trim($productName)));
    }
}
