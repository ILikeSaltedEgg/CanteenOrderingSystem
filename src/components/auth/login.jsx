import { useState, useEffect } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useNavigate, Link } from 'react-router-dom';
import { toast } from 'react-toastify';
import { login } from '../../redux/slices/authSlice';
import Loader from '../common/Loader';
import '../../assets/css/authForm.css';
 
const Login = () => {
  const [email, setEmail]       = useState('');
  const [password, setPassword] = useState('');
 
  const dispatch = useDispatch();
  const navigate = useNavigate();
 
  const { userInfo, isLoading, error } = useSelector((state) => state.auth);
 
  useEffect(() => {
    if (userInfo) {
      // Admin → admin dashboard, students → menu page directly
      navigate(userInfo.role === 'admin' ? '/admin' : '/menu');
    }
  }, [userInfo, navigate]);
 
  useEffect(() => {
    if (error) toast.error(error);
  }, [error]);
 
  const submitHandler = (e) => {
    e.preventDefault();
    dispatch(login({ email, password }));
  };
 
  return (
    <div className="auth-container">
 
      <Link to="/" className="auth-back-btn">
        ← Back to Home
      </Link>
 
      <form onSubmit={submitHandler} className="auth-form">
 
        <div className="auth-form-header">
          <Link to="/" className="auth-logo-link">
            <span className="auth-form-logo">🍱</span>
          </Link>
          <h2>Welcome back</h2>
          <p>Sign in to your canteen account</p>
        </div>
 
        <div className="form-group">
          <label>Email</label>
          <input
            type="email"
            placeholder="you@university.edu"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
          />
        </div>
 
        <div className="form-group">
          <label>Password</label>
          <input
            type="password"
            placeholder="Enter your password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            required
          />
        </div>
 
        <button type="submit" disabled={isLoading}>
          {isLoading ? <Loader /> : 'Sign In →'}
        </button>
 
        <p>
          Don't have an account? <Link to="/register">Create one</Link>
        </p>
 
      </form>
    </div>
  );
};
 
export default Login;
