import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import cartService from '../../services/cartService';

const initialState = {
  items: [],
  isLoading: false,
  error: null,
};

// Load cart from backend on login
export const fetchCart = createAsyncThunk('cart/fetchCart', async (_, thunkAPI) => {
  try {
    return await cartService.getCart();
  } catch (error) {
    return thunkAPI.rejectWithValue(error.response?.data?.message || error.message);
  }
});

export const addToCart = createAsyncThunk(
  'cart/addToCart',
  async ({ foodId, quantity }, thunkAPI) => {
    try {
      return await cartService.addToCart(foodId, quantity);
    } catch (error) {
      return thunkAPI.rejectWithValue(error.response?.data?.message || error.message);
    }
  }
);

export const updateCartItem = createAsyncThunk(
  'cart/updateCartItem',
  async ({ foodId, quantity }, thunkAPI) => {
    try {
      return await cartService.updateCartItem(foodId, quantity);
    } catch (error) {
      return thunkAPI.rejectWithValue(error.response?.data?.message || error.message);
    }
  }
);

export const removeFromCart = createAsyncThunk(
  'cart/removeFromCart',
  async (foodId, thunkAPI) => {
    try {
      await cartService.removeFromCart(foodId);
      return foodId;
    } catch (error) {
      return thunkAPI.rejectWithValue(error.response?.data?.message || error.message);
    }
  }
);

export const clearCart = createAsyncThunk('cart/clearCart', async (_, thunkAPI) => {
  try {
    await cartService.clearCart();
    return [];
  } catch (error) {
    return thunkAPI.rejectWithValue(error.response?.data?.message || error.message);
  }
});

const cartSlice = createSlice({
  name: 'cart',
  initialState,
  reducers: {},
  extraReducers: (builder) => {
    builder
      // fetchCart
      .addCase(fetchCart.fulfilled, (state, action) => {
        state.items = action.payload.items || [];
      })
      // addToCart
      .addCase(addToCart.fulfilled, (state, action) => {
        state.items = action.payload.items;
      })
      // updateCartItem
      .addCase(updateCartItem.fulfilled, (state, action) => {
        state.items = action.payload.items;
      })
      // removeFromCart
      .addCase(removeFromCart.fulfilled, (state, action) => {
        state.items = state.items.filter((item) => item.foodId !== action.payload);
      })
      // clearCart
      .addCase(clearCart.fulfilled, (state) => {
        state.items = [];
      });
  },
});

export default cartSlice.reducer;