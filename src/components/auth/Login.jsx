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
    <div className="auth-page-wrap">
      <div className="auth-login-card">
 
        {/* ── LEFT: Form ── */}
        <div className="auth-login-form-side">
          <form onSubmit={submitHandler} className="auth-form">
 
            <h2 className="auth-form-title">Welcome back.</h2>
            <p className="auth-form-subtitle">
              Access your curated university dining experience.
            </p>
 
            {/* Email */}
            <div className="form-group">
              <label>University Email</label>
              <input
                type="email"
                placeholder="name@university.edu"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required
              />
            </div>
 
            {/* Password with Forgot? */}
            <div className="form-group">
              <div className="auth-label-row">
                <label>Password</label>
                <a href="#forgot" className="auth-forgot-link">Forgot?</a>
              </div>
              <input
                type="password"
                placeholder="••••••••"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                required
              />
            </div>
 
            <button type="submit" disabled={isLoading}>
              {isLoading ? <Loader /> : 'Sign In'}
            </button>
 
            <p className="auth-footer-text">
              Dont have an account? <Link to="/register">Create one</Link>
            </p>
 
          </form>
        </div>
 
        {/* ── RIGHT: Greyscale image grid ── */}
        <div className="auth-login-img-side">
          {/*
            Drop in real images here when available, e.g.:
            <img src="/images/canteen-hall.jpg" alt="" />
            <img src="/images/canteen-food.jpg" alt="" />
            <img src="/images/canteen-counter.jpg" alt="" />
 
            Until then we use tonal placeholder tiles:
          */}
          <div className="auth-img-tile" />
          <div className="auth-img-tile" style={{ gridColumn: '1' }} />
          <div className="auth-img-tile" style={{ gridColumn: '2' }} />
        </div>
 
      </div>
    </div>
  );
};
 
export default Login;