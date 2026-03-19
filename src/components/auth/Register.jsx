import { useState, useEffect } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { useNavigate, Link } from 'react-router-dom';
import { toast } from 'react-toastify';
import { register } from '../../redux/slices/authSlice';
import Loader from '../common/Loader';
import '../../assets/css/authForm.css';
 
const Register = () => {
  const [name, setName]                       = useState('');
  const [email, setEmail]                     = useState('');
  const [password, setPassword]               = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
 
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
    if (password !== confirmPassword) {
      toast.error('Passwords do not match');
      return;
    }
    dispatch(register({ name, email, password }));
  };
 
  return (
    <div className="auth-container">
 
      <Link to="/" className="auth-back-btn">
        ← Back to Home
      </Link>
 
      <form onSubmit={submitHandler} className="auth-form">
 
        <div className="auth-form-header">
          <Link to="/" className="auth-logo-link">
            <span className="auth-form-logo">🎓</span>
          </Link>
          <h2>Create Account</h2>
          <p>Join the university canteen online</p>
        </div>
 
        <div className="form-group">
          <label>Full Name</label>
          <input
            type="text"
            placeholder="Juan dela Cruz"
            value={name}
            onChange={(e) => setName(e.target.value)}
            required
          />
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
            placeholder="Min. 6 characters"
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
            placeholder="Repeat your password"
            value={confirmPassword}
            onChange={(e) => setConfirmPassword(e.target.value)}
            required
            className={confirmPassword && confirmPassword !== password ? 'input-error' : ''}
          />
        </div>
 
        <button type="submit" disabled={isLoading}>
          {isLoading ? <Loader /> : 'Create Account →'}
        </button>
 
        <p>
          Already have an account? <Link to="/login">Sign in</Link>
        </p>
 
      </form>
    </div>
  );
};
 
export default Register;