import { useSelector } from 'react-redux';
import { Navigate } from 'react-router-dom';
import OrderSummary from '../components/order/OrderSummary';
 
const CheckoutPage = () => {
  const { items }       = useSelector((state) => state.cart);
  const { lastOrder }   = useSelector((state) => state.order);
 
  // Only redirect to cart if cart is empty AND no order was just placed.
  // Without the lastOrder check, the success screen vanishes instantly
  // because fetchCart() clears items and triggers this redirect.
  if (items.length === 0 && !lastOrder) {
    return <Navigate to="/cart" replace />;
  }
 
  return <OrderSummary />;
};
 
export default CheckoutPage;
 