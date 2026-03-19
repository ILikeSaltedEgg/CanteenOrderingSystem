import { useEffect } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { fetchMyOrders } from '../../redux/slices/orderSlice';
import OrderStatus from './OrderStatus';
import Loader from '../common/Loader';
import '../../assets/css/OrderHistory.css';
 
const OrderHistory = () => {
  const dispatch = useDispatch();
  const { orders, isLoading, error } = useSelector((state) => state.order);
 
  useEffect(() => {
    dispatch(fetchMyOrders());
  }, [dispatch]);
 
  if (isLoading) return <Loader />;
 
  if (error) {
    return (
      <div className="oh-wrapper">
        <div className="oh-error">Failed to load orders: {error}</div>
      </div>
    );
  }
 
  if (!orders || orders.length === 0) {
    return (
      <div className="oh-wrapper">
        <div className="oh-empty">
          <span className="oh-empty-icon">📋</span>
          <h3>No orders yet</h3>
          <p>Your order history will appear here once you place an order.</p>
        </div>
      </div>
    );
  }
 
  return (
    <div className="oh-wrapper">
      <div className="oh-header">
        <h2>My Orders</h2>
        <span className="oh-count">
          {orders.length} {orders.length === 1 ? 'order' : 'orders'}
        </span>
      </div>
 
      <div className="oh-list">
        {orders.map((order, i) => {
          // Supabase uses snake_case — support both for safety
          const orderId     = order.id           ?? order._id;
          const totalAmount = order.total_amount  ?? order.totalAmount  ?? 0;
          const createdAt   = order.created_at    ?? order.createdAt;
          const pickupTime  = order.pickup_time   ?? order.pickupTime;
          // Supabase returns order_items[], not items[]
          const items       = order.order_items   ?? order.items        ?? [];
 
          return (
            <div
              key={orderId}
              className="oh-card"
              style={{ animationDelay: `${i * 0.06}s` }}
            >
              {/* Header */}
              <div className="oh-card-header">
                <div className="oh-card-id">
                  <span className="oh-label">Order</span>
                  <strong>#{String(orderId).slice(-6).toUpperCase()}</strong>
                </div>
                <span className="oh-date">
                  {createdAt
                    ? new Date(createdAt).toLocaleDateString('en-PH', {
                        year: 'numeric', month: 'short', day: 'numeric',
                      })
                    : '—'}
                </span>
              </div>
 
              {/* Items list */}
              <div className="oh-items">
                {items.length === 0 ? (
                  <span className="oh-muted">No items</span>
                ) : (
                  items.map((item, idx) => (
                    <div key={item.id ?? item.food_id ?? idx} className="oh-item-row">
                      <span className="oh-item-name">{item.name}</span>
                      <span className="oh-item-qty">×{item.quantity}</span>
                      <span className="oh-item-price">
                        ₱{(Number(item.price) * item.quantity).toFixed(2)}
                      </span>
                    </div>
                  ))
                )}
              </div>
 
              {/* Footer */}
              <div className="oh-card-footer">
                <div className="oh-pickup">
                  <span className="oh-label">Pickup</span>
                  <span>
                    {pickupTime
                      ? new Date(pickupTime).toLocaleTimeString('en-PH', {
                          hour: '2-digit', minute: '2-digit', hour12: true,
                        })
                      : '—'}
                  </span>
                </div>
                <div className="oh-total">
                  <span className="oh-label">Total</span>
                  <strong>₱{Number(totalAmount).toFixed(2)}</strong>
                </div>
              </div>
 
              {/* Status tracker */}
              <div className="oh-status-wrap">
                <OrderStatus status={order.status} />
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
};
 
export default OrderHistory;