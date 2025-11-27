// cart page
document.addEventListener('DOMContentLoaded', function () {
  document.addEventListener('click', function(e) {
    const plus = e.target.closest('.woocommerce-cart-form .plus');
    const minus = e.target.closest('.woocommerce-cart-form .minus');

    if (!plus && !minus) return;

    const wrapper = plus?.closest('.quantity') || minus?.closest('.quantity');
    const input = wrapper.querySelector('input.qty');
    if (!input) return;

    const step = parseFloat(input.step) || 1;
    const min = parseFloat(input.min) || 0;
    const max = parseFloat(input.max) || Infinity;

    let val = parseFloat(input.value) || 0;

    if (plus && val < max) {
      input.value = (val + step).toFixed(step % 1 ? 2 : 0);
    }
    if (minus && val > min) {
      input.value = (val - step).toFixed(step % 1 ? 2 : 0);
    }

    input.dispatchEvent(new Event('change', { bubbles: true }));

    // Trigger WooCommerce cart update
    const updateBtn = document.querySelector('button[name="update_cart"]');
    if (updateBtn) {
      updateBtn.disabled = false;
      updateBtn.click();
    }
  });
});

