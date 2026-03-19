import { useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useNavigate } from 'react-router-dom';
import { toast } from 'react-toastify';
import { placeOrder } from '../../redux/slices/orderSlice';
import { fetchCart } from '../../redux/slices/cartSlice';
import Loader from '../common/Loader';
import '../../assets/css/Checkout.css';
 
// Generate pickup time options: every 15 min for the next 2 hours
const generatePickupTimes = () => {
  const times = [];
  const now = new Date();
  const start = new Date(now);
  start.setMinutes(Math.ceil((now.getMinutes() + 15) / 15) * 15, 0, 0);
 
  for (let i = 0; i < 8; i++) {
    const t = new Date(start.getTime() + i * 15 * 60 * 1000);
    times.push(t);
  }
  return times;
};
 
const formatTime = (date) =>
  date.toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit', hour12: true });
 
const OrderSummary = () => {
  const dispatch     = useDispatch();
  const navigate     = useNavigate();
  const { items }    = useSelector((state) => state.cart);
  const { isLoading, error } = useSelector((state) => state.order);
  const { userInfo } = useSelector((state) => state.auth);
 
  const pickupOptions = generatePickupTimes();
  const [selectedTime, setSelectedTime] = useState(pickupOptions[0]?.toISOString() || '');
  const [note, setNote]           = useState('');
  const [submitted, setSubmitted] = useState(false);
  const [placedOrder, setPlacedOrder] = useState(null);
  // Keep a snapshot of items + subtotal so the success screen
  // can display them even after the cart is cleared
  const [snapshot, setSnapshot]   = useState(null);
 
  const subtotal = items.reduce((acc, item) => acc + Number(item.price) * item.quantity, 0);
 
  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!selectedTime) {
      toast.error('Please select a pickup time');
      return;
    }
 
    // Snapshot cart BEFORE dispatching so success screen has the data
    const cartSnapshot = {
      items:    [...items],
      subtotal: subtotal,
    };
 
    const result = await dispatch(placeOrder({ pickupTime: selectedTime, note }));
 
    if (placeOrder.fulfilled.match(result)) {
      setPlacedOrder(result.payload);
      setSnapshot(cartSnapshot);
      setSubmitted(true);
      toast.success('Order placed successfully! 🎉');
      // Fetch cart AFTER setting submitted=true so CheckoutPage
      // doesn't redirect before the success screen renders
      dispatch(fetchCart());
    } else {
      toast.error(result.payload || 'Failed to place order');
    }
  };
 
  // ── SUCCESS SCREEN ─────────────────────────────────────────
  if (submitted && placedOrder && snapshot) {
    const orderId = placedOrder.id ?? placedOrder._id;
 
    return (
      <div className="co-success">
        <div className="co-success-icon">✅</div>
        <h2>Order Placed!</h2>
        <p>Your order has been received. Head to the canteen at your pickup time.</p>
 
        <div className="co-success-card">
          <div className="co-success-row">
            <span>Order ID</span>
            <strong>#{String(orderId).slice(-6).toUpperCase()}</strong>
          </div>
          <div className="co-success-row">
            <span>Pickup Time</span>
            <strong>{formatTime(new Date(selectedTime))}</strong>
          </div>
          <div className="co-success-row">
            <span>Items</span>
            <div className="co-success-items">
              {snapshot.items.map((item) => (
                <span key={item.foodId} className="co-success-item-line">
                  {item.name} ×{item.quantity}
                </span>
              ))}
            </div>
          </div>
          <div className="co-success-row">
            <span>Total</span>
            <strong>₱{snapshot.subtotal.toFixed(2)}</strong>
          </div>
          <div className="co-success-row">
            <span>Status</span>
            <span className="co-badge co-badge-pending">Pending</span>
          </div>
        </div>
 
        <div className="co-success-actions">
          <button className="co-btn-primary" onClick={() => navigate('/orders')}>
            View My Orders
          </button>
          <button className="co-btn-secondary" onClick={() => navigate('/menu')}>
            Order More
          </button>
        </div>
      </div>
    );
  }
 
  // ── CHECKOUT FORM ──────────────────────────────────────────
  return (
    <div className="co-layout">
 
      {/* Left — Form */}
      <div className="co-form-section">
        <div className="co-section-header">
          <h2>Checkout</h2>
          <p>Review your order and choose a pickup time</p>
        </div>
 
        <form onSubmit={handleSubmit} className="co-form">
 
          {/* Customer info */}
          <div className="co-card">
            <div className="co-card-title">👤 Customer</div>
            <div className="co-info-row">
              <span>{userInfo?.name || 'Student'}</span>
              <span className="co-muted">{userInfo?.email}</span>
            </div>
          </div>
 
          {/* Pickup time */}
          <div className="co-card">
            <div className="co-card-title">🕐 Pickup Time</div>
            <p className="co-card-sub">Select when you'll pick up your order at the canteen.</p>
            <div className="co-time-grid">
              {pickupOptions.map((t) => {
                const iso = t.toISOString();
                return (
                  <button
                    key={iso}
                    type="button"
                    className={`co-time-btn ${selectedTime === iso ? 'selected' : ''}`}
                    onClick={() => setSelectedTime(iso)}
                  >
                    {formatTime(t)}
                  </button>
                );
              })}
            </div>
          </div>
 
          {/* Special note */}
          <div className="co-card">
            <div className="co-card-title">
              📝 Special Instructions{' '}
              <span className="co-optional">(optional)</span>
            </div>
            <textarea
              className="co-textarea"
              placeholder="e.g. No onions, extra rice..."
              value={note}
              onChange={(e) => setNote(e.target.value)}
              rows={3}
            />
          </div>
 
          {error && <div className="co-error">{error}</div>}
 
          <button type="submit" className="co-submit-btn" disabled={isLoading}>
            {isLoading ? <Loader /> : `Place Order · ₱${subtotal.toFixed(2)}`}
          </button>
 
        </form>
      </div>
 
      {/* Right — Order summary */}
      <div className="co-summary">
        <div className="co-summary-title">Order Summary</div>
 
        <div className="co-summary-items">
          {items.map((item) => (
            <div key={item.foodId} className="co-summary-item">
              <div className="co-summary-item-info">
                <span className="co-summary-item-name">{item.name}</span>
                <span className="co-summary-item-qty">×{item.quantity}</span>
              </div>
              <span className="co-summary-item-price">
                ₱{(Number(item.price) * item.quantity).toFixed(2)}
              </span>
            </div>
          ))}
        </div>
 
        <div className="co-summary-divider" />
 
        <div className="co-summary-subtotal">
          <span>Subtotal</span>
          <span>₱{subtotal.toFixed(2)}</span>
        </div>
        <div className="co-summary-fee">
          <span>Service Fee</span>
          <span className="co-muted">Free</span>
        </div>
 
        <div className="co-summary-divider" />
 
        <div className="co-summary-total">
          <span>Total</span>
          <span>₱{subtotal.toFixed(2)}</span>
        </div>
 
        <div className="co-summary-note">
          🎓 University canteen · Pickup only
        </div>
      </div>
 
    </div>
  );
};
 
export default OrderSummary;