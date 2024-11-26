// assets/shop/js/quantity-validation.js
document.addEventListener('DOMContentLoaded', () => {
  const initializeQuantityValidation = () => {
    const quantityInput = document.getElementById('sylius_shop_add_to_cart_cartItem_quantity')
    const addToCartForm = document.querySelector('form[name="sylius_shop_add_to_cart"]')

    // Prevent multiple initializations
    if (quantityInput?.dataset.initialized || addToCartForm?.dataset.initialized) {
      return
    }

    if (quantityInput && addToCartForm) {
      // Mark as initialized
      quantityInput.dataset.initialized = 'true'
      addToCartForm.dataset.initialized = 'true'

      // Set min and step attributes
      quantityInput.setAttribute('min', '10')
      quantityInput.setAttribute('step', '10')

      // Add validation on input change
      quantityInput.addEventListener('change', (event) => {
        const value = parseInt(event.target.value)
        let newValue = value

        if (value < 10) {
          newValue = 10
        } else {
          // Round to nearest multiple of 10
          newValue = Math.ceil(value / 10) * 10
        }

        event.target.value = newValue

        // Check if quantity is 70 after rounding
        if (newValue === 70) {
          setTimeout(() => {
            alert('Great Choice!')
          }, 100)
        }
      })

      // Handle add to cart click
      const addToCartButton = addToCartForm.querySelector('button[type="submit"]')
      if (addToCartButton) {
        addToCartButton.addEventListener('click', (event) => {
          const quantity = parseInt(quantityInput.value)
          // Validate multiple of 10
          if (quantity % 10 !== 0) {
            event.preventDefault()
            event.stopPropagation()
            alert('Products can only be ordered in multiples of 10')
            return false
          }
        }, true)
      }
    }
  }

  // Initialize on page load and every second (to catch dynamic form loading)
  initializeQuantityValidation()
  const initInterval = setInterval(() => {
    const form = document.querySelector('form[name="sylius_shop_add_to_cart"]')
    if (form) {
      initializeQuantityValidation()
      clearInterval(initInterval)
    }
  }, 1000)
})
