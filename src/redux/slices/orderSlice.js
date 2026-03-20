import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import orderService from '../../services/orderService';
 
const initialState = {
  orders:      [],
  dailyOrders: { orders: [], totalOrders: 0, totalRevenue: 0 },
  lastOrder:   null,  
  isLoading:   false,
  error:       null,
};
 
export const placeOrder = createAsyncThunk(
  'order/placeOrder',
  async (orderData, thunkAPI) => {
    try {
      return await orderService.placeOrder(orderData);
    } catch (error) {
      return thunkAPI.rejectWithValue(error.response?.data?.message || error.message);
    }
  }
);
 
export const fetchMyOrders = createAsyncThunk('order/fetchMyOrders', async (_, thunkAPI) => {
  try {
    return await orderService.getMyOrders();
  } catch (error) {
    return thunkAPI.rejectWithValue(error.response?.data?.message || error.message);
  }
});
 
export const fetchAllOrders = createAsyncThunk('order/fetchAllOrders', async (_, thunkAPI) => {
  try {
    return await orderService.getAllOrders();
  } catch (error) {
    return thunkAPI.rejectWithValue(error.response?.data?.message || error.message);
  }
});
 
export const fetchDailyOrders = createAsyncThunk('order/fetchDailyOrders', async (_, thunkAPI) => {
  try {
    return await orderService.getDailyOrders();
  } catch (error) {
    return thunkAPI.rejectWithValue(error.response?.data?.message || error.message);
  }
});
 
export const updateOrderStatus = createAsyncThunk(
  'order/updateOrderStatus',
  async ({ orderId, status }, thunkAPI) => {
    try {
      return await orderService.updateOrderStatus(orderId, status);
    } catch (error) {
      return thunkAPI.rejectWithValue(error.response?.data?.message || error.message);
    }
  }
);
 
const orderSlice = createSlice({
  name: 'order',
  initialState,
  reducers: {
    clearLastOrder: (state) => {
      state.lastOrder = null;
    },
  },
  extraReducers: (builder) => {
    builder
     .addCase(placeOrder.pending, (state) => {
        state.isLoading = true;
        state.error     = null;
      })
      .addCase(placeOrder.fulfilled, (state, action) => {
        state.isLoading = false;
        state.lastOrder = action.payload;   // ← save so CheckoutPage won't redirect
        state.orders.push(action.payload);
      })
      .addCase(placeOrder.rejected, (state, action) => {
        state.isLoading = false;
        state.error     = action.payload;
      })
     .addCase(fetchMyOrders.pending, (state) => {
        state.isLoading = true;
        state.error     = null;
      })
      .addCase(fetchMyOrders.fulfilled, (state, action) => {
        state.isLoading = false;
        state.orders    = action.payload;
      })
      .addCase(fetchMyOrders.rejected, (state, action) => {
        state.isLoading = false;
        state.error     = action.payload;
      })
      .addCase(fetchAllOrders.pending,   (state)         => { state.isLoading = true; })
      .addCase(fetchAllOrders.fulfilled, (state, action) => {
        state.isLoading = false;
        state.orders    = action.payload;
      })
      .addCase(fetchAllOrders.rejected,  (state, action) => {
        state.isLoading = false;
        state.error     = action.payload;
      })
      // fetchDailyOrders
      .addCase(fetchDailyOrders.pending,   (state)         => { state.isLoading = true; })
      .addCase(fetchDailyOrders.fulfilled, (state, action) => {
        state.isLoading  = false;
        state.dailyOrders = action.payload;
      })
      .addCase(fetchDailyOrders.rejected,  (state, action) => {
        state.isLoading = false;
        state.error     = action.payload;
      })
      // updateOrderStatus
      .addCase(updateOrderStatus.fulfilled, (state, action) => {
        const updated   = action.payload;
        const updatedId = updated.id ?? updated._id;
 
        const index = state.orders.findIndex((o) => (o.id ?? o._id) === updatedId);
        if (index !== -1) state.orders[index] = updated;
 
        if (state.dailyOrders.orders) {
          const dailyIndex = state.dailyOrders.orders.findIndex(
            (o) => (o.id ?? o._id) === updatedId
          );
          if (dailyIndex !== -1) state.dailyOrders.orders[dailyIndex] = updated;
        }
      });
  },
});
 
export const { clearLastOrder } = orderSlice.actions;
export default orderSlice.reducer;
