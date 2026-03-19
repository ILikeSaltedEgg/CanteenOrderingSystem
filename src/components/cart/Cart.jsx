import React from 'react';
import { useSelector, useDispatch } from 'react-redux';
import { Link } from 'react-router-dom';
import CartItem from './CartItem';
import { clearCart } from '../../redux/slices/cartSlice';
import '../../assets/css/Cart.css';
 
const Cart = () => {
  const dispatch = useDispatch();
  const { items } = useSelector((state) => state.cart);
 
  const subtotal = items.reduce(
    (acc, item) => acc + Number(item.price) * item.quantity,
    0
  );
 
  if (items.length === 0) {
    return (
      <div className="cart-empty">
        <div className="cart-empty-icon">🛒</div>
        <h2>Your cart is empty</h2>
        <p>Looks like you haven't added anything yet.</p>
        <Link to="/menu" className="cart-browse-btn">Browse Menu →</Link>
      </div>
    );
  }
 
  return (
    <div className="cart-layout">
 
      <div className="cart-items-section">
        <div className="cart-section-header">
          <h2>Your Cart</h2>
          <span className="cart-count">{items.length} {items.length === 1 ? 'item' : 'items'}</span>
        </div>
 
        <div className="cart-items-list">
          {items.map((item) => (
            <CartItem key={item.foodId} item={item} />
          ))}
        </div>
 
        <button
          className="cart-clear-btn"
          onClick={() => dispatch(clearCart())}
        >
          🗑 Clear Cart
        </button>
      </div>
 
      {/* Right — Summary */}
      <div className="cart-summary">
        <h3>Order Summary</h3>
 
        <div className="cart-summary-rows">
          {items.map((item) => (
            <div key={item.foodId} className="cart-summary-row">
              <span>{item.name} <em>×{item.quantity}</em></span>
              <span>₱{(Number(item.price) * item.quantity).toFixed(2)}</span>
            </div>
          ))}
        </div>
 
        <div className="cart-summary-divider" />
 
        <div className="cart-summary-total">
          <span>Total</span>
          <span>₱{subtotal.toFixed(2)}</span>
        </div>
 
        <Link to="/checkout" className="cart-checkout-btn">
          Proceed to Checkout →
        </Link>
 
        <Link to="/menu" className="cart-continue-link">
          ← Continue Shopping
        </Link>
      </div>
 
    </div>
  );
};
 
export default Cart;
