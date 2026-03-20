import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import foodService from '../../services/foodService';

const initialState = {
  foods: [],
  isLoading: false,
  error: null,
};

export const fetchFoods = createAsyncThunk(
  'food/fetchFoods',
  async (filters, thunkAPI) => {
    try {
      return await foodService.getFoods(filters);
    } catch (error) {
      return thunkAPI.rejectWithValue(error.response?.data?.message || error.message);
    }
  }
);

export const createFood = createAsyncThunk(
  'food/createFood',
  async (foodData, thunkAPI) => {
    try {
      return await foodService.createFood(foodData);
    } catch (error) {
      return thunkAPI.rejectWithValue(error.response?.data?.message || error.message);
    }
  }
);

export const updateFood = createAsyncThunk(
  'food/updateFood',
  async ({ id, foodData }, thunkAPI) => {
    try {
      return await foodService.updateFood(id, foodData);
    } catch (error) {
      return thunkAPI.rejectWithValue(error.response?.data?.message || error.message);
    }
  }
);

export const deleteFood = createAsyncThunk(
  'food/deleteFood',
  async (id, thunkAPI) => {
    try {
      await foodService.deleteFood(id);
      return id;
    } catch (error) {
      return thunkAPI.rejectWithValue(error.response?.data?.message || error.message);
    }
  }
);

const foodSlice = createSlice({
  name: 'food',
  initialState,
  reducers: {},
  extraReducers: (builder) => {
    builder
      .addCase(fetchFoods.pending, (state) => {
        state.isLoading = true;
      })
      .addCase(fetchFoods.fulfilled, (state, action) => {
        state.isLoading = false;
        state.foods = action.payload;
      })
      .addCase(fetchFoods.rejected, (state, action) => {
        state.isLoading = false;
        state.error = action.payload;
      })
      // createFood
      .addCase(createFood.fulfilled, (state, action) => {
        state.foods.push(action.payload);
      })
      // updateFood
      .addCase(updateFood.fulfilled, (state, action) => {
        const index = state.foods.findIndex((f) => f._id === action.payload._id);
        if (index !== -1) state.foods[index] = action.payload;
      })
      // deleteFood
      .addCase(deleteFood.fulfilled, (state, action) => {
        state.foods = state.foods.filter((f) => f._id !== action.payload);
      });
  },
});

export default foodSlice.reducer;
