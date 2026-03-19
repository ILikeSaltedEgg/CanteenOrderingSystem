import { useEffect } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { fetchDailyOrders } from '../../redux/slices/orderSlice';
import Loader from '../common/Loader';
 
const badgeClass = (status = '') => {
  const map = {
    'pending':          'badge-pending',
    'preparing':        'badge-preparing',
    'ready for pickup': 'badge-ready',
    'completed':        'badge-completed',
    'cancelled':        'badge-unavailable',
  };
  return map[status.toLowerCase()] || 'badge-pending';
};
 
const DailyOrders = () => {
  const dispatch = useDispatch();
  const { dailyOrders, isLoading } = useSelector((state) => state.order);
 
  useEffect(() => {
    dispatch(fetchDailyOrders());
  }, [dispatch]);
 
  if (isLoading) return <Loader />;
 
  const orders = dailyOrders.orders ?? [];
 
  return (
    <div>
      <div className="page-header">
        <div>
          <h2>Daily Orders</h2>
          <p>Summary for today</p>
        </div>
      </div>
 
      <div className="stats">
        <p>
          Total Orders
          <span>{dailyOrders.totalOrders ?? '—'}</span>
        </p>
        <p>
          Total Revenue
          <span>₱{Number(dailyOrders.totalRevenue ?? 0).toFixed(2)}</span>
        </p>
      </div>
 
      <div className="panel">
        <div className="panel-header">
          <span className="panel-title">Today's Orders</span>
        </div>
        <div className="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>Order ID</th>
                <th>User</th>
                <th>Items</th>
                <th>Total</th>
                <th>Pickup Time</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              {orders.length === 0 ? (
                <tr>
                  <td colSpan="6">
                    <div className="empty-state">
                      <p>No orders for today yet.</p>
                    </div>
                  </td>
                </tr>
              ) : (
                orders.map((order) => {
                  const orderId  = order.id  ?? order._id;
                  const total    = order.total_amount ?? order.totalAmount ?? 0;
                  const pickup   = order.pickup_time  ?? order.pickupTime;
                  const userName = order.users?.name  ?? order.user?.name ?? '—';
                  const items    = order.order_items ?? order.items ?? [];
 
                  return (
                    <tr key={orderId}>
                      <td className="td-mono">
                        #{String(orderId).slice(-6).toUpperCase()}
                      </td>
                      <td>{userName}</td>
                      <td>
                        {items.length === 0 ? (
                          <span style={{ color: '#999', fontSize: '12px' }}>—</span>
                        ) : (
                          items.map((item, i) => (
                            <div key={item.id ?? item.food_id ?? i} style={{ fontSize: '12px', color: '#555' }}>
                              {item.name} <span style={{ color: '#999' }}>×{item.quantity}</span>
                            </div>
                          ))
                        )}
                      </td>
                      <td className="td-mono">₱{Number(total).toFixed(2)}</td>
                      <td style={{ fontSize: '12px', color: '#666' }}>
                        {pickup ? new Date(pickup).toLocaleTimeString() : '—'}
                      </td>
                      <td>
                        <span className={`badge ${badgeClass(order.status)}`}>
                          {order.status}
                        </span>
                      </td>
                    </tr>
                  );
                })
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};
 
export default DailyOrders;
