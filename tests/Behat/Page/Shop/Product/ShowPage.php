<?php

declare(strict_types=1);

namespace App\Tests\Behat\Page\Shop\Product;

use Sylius\Behat\Page\Shop\Product\ShowPage as BaseShowPage;
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;

class ShowPage extends BaseShowPage
{
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'quantity_input' => '[name="sylius_shop_add_to_cart[cartItem][quantity]"]',
            'add_to_cart_button' => 'button[type="submit"]',
            'validation_error' => '.sylius-validation-error',
            'success_message' => '.sylius-flash-message.positive'
        ]);
    }

    public function specifyQuantity(int $quantity): void
    {
        $this->getElement('quantity_input')->setValue($quantity);
    }

    public function addToCart(): void
    {
        $this->getElement('add_to_cart_button')->click();
    }

    public function getValidationMessage(string $element = 'validation_error', array $parameters = []): string
    {
        return $this->getElement($element)->getText();
    }

    protected function verifyUrl(array $urlParameters = []): void
    {
        $slug = $urlParameters['slug'] ?? '';
        $expectedUrl = $this->getUrl(['slug' => $slug]);
        $actualUrl = $this->getSession()->getCurrentUrl();

        if ($expectedUrl !== $actualUrl) {
            throw new UnexpectedPageException(sprintf(
                'Expected to be on "%s" but found "%s" instead',
                $expectedUrl,
                $actualUrl
            ));
        }
    }

    public function verifyRoute(array $requiredUrlParameters = []): void
    {
        $this->verifyUrl($requiredUrlParameters);
    }

    protected function getUrl(array $urlParameters = []): string
    {
        return $this->getParameter('base_url') . '/en_US/products/' . $urlParameters['slug'];
    }

    public function getAlertText(): ?string
    {
        try {
            return $this->getDriver()->evaluateScript("window.getLastAlert");
        } catch (\Exception $e) {
            return null;
        }
    }

    public function open(array $urlParameters = []): void
    {
        $url = $this->getUrl($urlParameters);
        $this->getSession()->visit($url);
        $this->verifyUrl($urlParameters);

        // Add script for capturing alerts
        $this->getSession()->executeScript(<<<JS
           window.lastAlert = null;
           window.originalAlert = window.alert;
           window.alert = function(message) {
               window.lastAlert = message;
               return window.originalAlert(message);
           };
           window.getLastAlert = function() {
               return window.lastAlert;
           };
       JS);
    }
}
