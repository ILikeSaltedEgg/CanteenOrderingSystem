import api from './api';

const getCart = async () => {
  const response = await api.get('/cart');
  return response.data;
};

const addToCart = async (foodId, quantity) => {
  const response = await api.post('/cart', { foodId, quantity });
  return response.data;
};

const updateCartItem = async (foodId, quantity) => {
  const response = await api.put(`/cart/item/${foodId}`, { quantity });
  return response.data;
};

const removeFromCart = async (foodId) => {
  const response = await api.delete(`/cart/item/${foodId}`);
  return response.data;
};

const clearCart = async () => {
  const response = await api.delete('/cart');
  return response.data;
};

const cartService = {
  getCart,
  addToCart,
  updateCartItem,
  removeFromCart,
  clearCart,
};

export default cartService;