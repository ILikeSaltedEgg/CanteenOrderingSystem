import api from './api';

const placeOrder = async (orderData) => {
  const response = await api.post('/orders', orderData);
  return response.data;
};

const getMyOrders = async () => {
  const response = await api.get('/orders/myorders');
  return response.data;
};

const getAllOrders = async () => {
  const response = await api.get('/admin/orders');
  return response.data;
};

const getDailyOrders = async () => {
  const response = await api.get('/admin/daily-orders');
  return response.data;
};

const updateOrderStatus = async (orderId, status) => {
  const response = await api.put(`/admin/orders/${orderId}/status`, { status });
  return response.data;
};

const orderService = {
  placeOrder,
  getMyOrders,
  getAllOrders,
  getDailyOrders,
  updateOrderStatus,
};

export default orderService;