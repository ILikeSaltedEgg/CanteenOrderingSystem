import { useSelector } from 'react-redux';
import { Navigate } from 'react-router-dom';
import OrderSummary from '../components/order/OrderSummary';
 
const CheckoutPage = () => {
  const { items }       = useSelector((state) => state.cart);
  const { lastOrder }   = useSelector((state) => state.order);
 
  // Only redirect to cart if cart is empty AND no order was just placed.
  if (items.length === 0 && !lastOrder) {
    return <Navigate to="/cart" replace />;
  }
 
  return <OrderSummary />;
};
 
export default CheckoutPage;
 