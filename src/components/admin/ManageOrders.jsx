import { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { fetchAllOrders, updateOrderStatus } from '../../redux/slices/orderSlice';
import Loader from '../common/Loader';
 
const STATUS_OPTIONS = ['Pending', 'Preparing', 'Ready for Pickup', 'Completed'];
 
const badgeClass = (status) => {
  const map = {
    'Pending':          'badge-pending',
    'Preparing':        'badge-preparing',
    'Ready for Pickup': 'badge-ready',
    'Completed':        'badge-completed',
  };
  return map[status] || 'badge-pending';
};
 
const ManageOrders = () => {
  const dispatch = useDispatch();
  const { orders, isLoading } = useSelector((state) => state.order);
  const [filter, setFilter] = useState('');
 
  useEffect(() => {
    dispatch(fetchAllOrders());
  }, [dispatch]);
 
  const handleStatusChange = (orderId, newStatus) => {
    dispatch(updateOrderStatus({ orderId, status: newStatus }));
  };
 
  const filteredOrders = filter
    ? orders.filter((order) => order.status === filter)
    : orders;
 
  if (isLoading) return <Loader />;
 
  return (
    <div>
      <div className="page-header">
        <div>
          <h2>Manage Orders</h2>
          <p>{filteredOrders.length} orders shown</p>
        </div>
      </div>
 
      <div className="filter-bar">
        <label>Filter by status:</label>
        <select value={filter} onChange={(e) => setFilter(e.target.value)}>
          <option value="">All Orders</option>
          {STATUS_OPTIONS.map((s) => (
            <option key={s} value={s}>{s}</option>
          ))}
        </select>
      </div>
 
      <div className="panel">
        <div className="panel-header">
          <span className="panel-title">Orders</span>
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
                <th>Update</th>
              </tr>
            </thead>
            <tbody>
              {filteredOrders.length === 0 ? (
                <tr>
                  <td colSpan="7">
                    <div className="empty-state">
                      <p>No orders found.</p>
                    </div>
                  </td>
                </tr>
              ) : (
                filteredOrders.map((order) => {
                  // Supabase uses: id, order_items, total_amount, pickup_time, users(name)
                  const orderId   = order.id ?? order._id;
                  const items     = order.order_items ?? order.items ?? [];
                  const total     = order.total_amount ?? order.totalAmount ?? 0;
                  const pickup    = order.pickup_time  ?? order.pickupTime;
                  const userName  = order.users?.name  ?? order.user?.name ?? order.user ?? '—';
 
                  return (
                    <tr key={orderId}>
                      <td className="td-mono">
                        #{String(orderId).slice(-6).toUpperCase()}
                      </td>
                      <td>{userName}</td>
                      <td>
                        {items.map((item, i) => (
                          <div key={item.id ?? item.food_id ?? i} style={{ fontSize: '12px', color: '#555' }}>
                            {item.name} <span style={{ color: '#999' }}>×{item.quantity}</span>
                          </div>
                        ))}
                      </td>
                      <td className="td-mono">₱{Number(total).toFixed(2)}</td>
                      <td style={{ fontSize: '12px', color: '#666' }}>
                        {pickup ? new Date(pickup).toLocaleString() : '—'}
                      </td>
                      <td>
                        <span className={`badge ${badgeClass(order.status)}`}>
                          {order.status}
                        </span>
                      </td>
                      <td>
                        <select
                          value={order.status}
                          onChange={(e) => handleStatusChange(orderId, e.target.value)}
                          style={{ width: 'auto', minWidth: '140px' }}
                        >
                          {STATUS_OPTIONS.map((s) => (
                            <option key={s} value={s}>{s}</option>
                          ))}
                        </select>
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
 
export default ManageOrders;