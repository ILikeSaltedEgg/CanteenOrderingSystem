import { useDispatch } from 'react-redux';
import { updateCartItem, removeFromCart } from '../../redux/slices/cartSlice';
import { FaTrash } from 'react-icons/fa';
 
const CartItem = ({ item }) => {
  const dispatch = useDispatch();
 
  const increment = () => {
    dispatch(updateCartItem({ foodId: item.foodId, quantity: item.quantity + 1 }));
  };
 
  const decrement = () => {
    if (item.quantity > 1) {
      dispatch(updateCartItem({ foodId: item.foodId, quantity: item.quantity - 1 }));
    } else {
      dispatch(removeFromCart(item.foodId));
    }
  };
 
  return (
    <div className="cart-item">
 
      {/* Image or placeholder */}
      <div className="cart-item-img">
        {item.image ? (
          <img src={item.image} alt={item.name} />
        ) : (
          <span className="cart-item-img-placeholder">🍽️</span>
        )}
      </div>
 
      {/* Info */}
      <div className="cart-item-info">
        <h4 className="cart-item-name">{item.name}</h4>
        <p className="cart-item-price">₱{Number(item.price).toFixed(2)} each</p>
      </div>
 
      {/* Quantity controls */}
      <div className="cart-item-qty">
        <button className="qty-btn" onClick={decrement}>−</button>
        <span className="qty-value">{item.quantity}</span>
        <button className="qty-btn" onClick={increment}>+</button>
      </div>
 
      {/* Line total */}
      <div className="cart-item-total">
        ₱{(Number(item.price) * item.quantity).toFixed(2)}
      </div>
 
      {/* Remove */}
      <button
        className="cart-item-remove"
        onClick={() => dispatch(removeFromCart(item.foodId))}
        title="Remove item"
      >
        <FaTrash />
      </button>
 
    </div>
  );
};
 
export default CartItem;