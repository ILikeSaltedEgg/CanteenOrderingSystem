import { useState, useEffect } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useNavigate, Link } from 'react-router-dom';
import { toast } from 'react-toastify';
import { register } from '../../redux/slices/authSlice';
import Loader from '../common/Loader';
import '../../assets/css/authForm.css';
 
const Register = () => {
  const [name,            setName]            = useState('');
  const [email,           setEmail]           = useState('');
  const [password,        setPassword]        = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
 
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
    if (password !== confirmPassword) {
      toast.error('Passwords do not match');
      return;
    }
    dispatch(register({ name, email, password }));
  };
 
  return (
    <div className="auth-page-wrap">
      <div className="auth-register-card">
 
        {/* ── LEFT: Dark editorial panel ── */}
        <div className="auth-register-panel">
          {/*
            Optional background image — uncomment when you have one:
            <img src="/images/canteen-dark.jpg" alt="" />
          */}
          <div className="auth-register-panel-content">
            <p className="auth-register-panel-eyebrow">Join the Community</p>
            <h2 className="auth-register-panel-title">
              The Student<br />Curator.
            </h2>
            <p className="auth-register-panel-desc">
              Access curated meals and nutritional tracking designed for the modern scholar.
            </p>
          </div>
        </div>
 
        <div className="auth-register-form-side">
          <form onSubmit={submitHandler} className="auth-form">
 
            <h2 className="auth-form-title">Create Account</h2>
            <p className="auth-form-subtitle">
              Enter your details below to start your editorial dining journey.
            </p>
 
            {/* Full Name */}
            <div className="form-group">
              <label>Full Name</label>
              <input
                type="text"
                placeholder="Alex Rivers"
                value={name}
                onChange={(e) => setName(e.target.value)}
                required
              />
            </div>
 
            {/* Email */}
            <div className="form-group">
              <label>University Email</label>
              <input
                type="email"
                placeholder="a.rivers@university.edu"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required
              />
            </div>
 
            {/* Password + Confirm — two columns */}
            <div className="form-row-2">
              <div className="form-group">
                <label>Password</label>
                <input
                  type="password"
                  placeholder="••••••••"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  required
                  minLength={6}
                />
              </div>
              <div className="form-group">
                <label>Confirm Password</label>
                <input
                  type="password"
                  placeholder="••••••••"
                  value={confirmPassword}
                  onChange={(e) => setConfirmPassword(e.target.value)}
                  required
                  className={
                    confirmPassword && confirmPassword !== password
                      ? 'input-error'
                      : ''
                  }
                />
              </div>
            </div>
 
            <button type="submit" disabled={isLoading}>
              {isLoading ? <Loader /> : 'Create Account'}
            </button>
 
            <p className="auth-footer-text">
              Already have an account? <Link to="/login">Login here</Link>
            </p>
 
          </form>
        </div>
 
      </div>
    </div>
  );
};
 
export default Register;