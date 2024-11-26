# features/shop/cart/adding_products_in_batches.feature
@adding_to_cart @ui
Feature: Adding products to cart in batches of 10
    In order to follow company's batch policy
    As a Customer
    I want to add products to cart only in multiples of 10

    Background:
        Given the store operates on a channel named "Fashion Web Store"
        And the store has a product "Stellar Drift T-Shirt" priced at 10000
        And I am a logged in customer

    @javascript
    Scenario: Adding a valid batch quantity to cart
        Given I am on the product page for "Stellar Drift T-Shirt"
        When I set quantity to 10
        And I add it to the cart
        Then I should be notified that the product has been successfully added
        And I should see "Stellar Drift T-Shirt" with quantity 10 in my cart

    @javascript
    Scenario: Cannot add invalid batch quantity
        Given I am on the product page for "Stellar Drift T-Shirt"
        When I set quantity to 5
        And I try to add it to the cart
        Then I should be notified that products can only be ordered in multiples of 10

    @javascript
    Scenario: Showing special message for specific quantity
        Given I am on the product page for "Stellar Drift T-Shirt"
        When I set quantity to 70
        And I add it to the cart
        Then I should see "Great Choice!" message
