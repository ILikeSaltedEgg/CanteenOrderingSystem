import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import authService from '../../services/authService';
 
// Rehydrate from localStorage on page load
const user = JSON.parse(localStorage.getItem('user'));
 
const initialState = {
  userInfo: user || null,
  isLoading: false,
  error: null,
};
 
export const login = createAsyncThunk(
  'auth/login',
  async ({ email, password }, thunkAPI) => {
    try {
      const data = await authService.login(email, password);
      localStorage.setItem('user', JSON.stringify(data));
      return data;
    } catch (error) {
      return thunkAPI.rejectWithValue(error.response?.data?.message || error.message);
    }
  }
);
 
export const register = createAsyncThunk(
  'auth/register',
  async (userData, thunkAPI) => {
    try {
      const data = await authService.register(userData);
      localStorage.setItem('user', JSON.stringify(data));
      return data;
    } catch (error) {
      return thunkAPI.rejectWithValue(error.response?.data?.message || error.message);
    }
  }
);
 
const authSlice = createSlice({
  name: 'auth',
  initialState,
  reducers: {
    // logout is synchronous — no need for createAsyncThunk
    // This is what AdminDashboard.jsx dispatches: dispatch(logout())
    logout: (state) => {
      localStorage.removeItem('user');
      state.userInfo = null;
      state.error = null;
    },
    clearError: (state) => {
      state.error = null;
    },
  },
  extraReducers: (builder) => {
    builder
      // Login
      .addCase(login.pending, (state) => {
        state.isLoading = true;
        state.error = null;
      })
      .addCase(login.fulfilled, (state, action) => {
        state.isLoading = false;
        state.userInfo = action.payload;
      })
      .addCase(login.rejected, (state, action) => {
        state.isLoading = false;
        state.error = action.payload;
      })
      // Register
      .addCase(register.pending, (state) => {
        state.isLoading = true;
        state.error = null;
      })
      .addCase(register.fulfilled, (state, action) => {
        state.isLoading = false;
        state.userInfo = action.payload;
      })
      .addCase(register.rejected, (state, action) => {
        state.isLoading = false;
        state.error = action.payload;
      });
  },
});
 
export const { logout, clearError } = authSlice.actions;
export default authSlice.reducer;