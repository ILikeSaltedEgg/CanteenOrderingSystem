import { Link, useNavigate } from 'react-router-dom';
import { useSelector, useDispatch } from 'react-redux';
import { FaShoppingCart, FaSignOutAlt, FaClipboardList } from 'react-icons/fa';
import { logout } from '../../redux/slices/authSlice';
 
const Header = () => {
  const { userInfo } = useSelector((state) => state.auth);
  const { items }    = useSelector((state) => state.cart);
  const dispatch     = useDispatch();
  const navigate     = useNavigate();
 
  const logoutHandler = () => {
    dispatch(logout());
    navigate('/login');
  };
 
  const cartCount = items.reduce((acc, item) => acc + item.quantity, 0);
 
  return (
    <header className="header">
      <div className="container">
        <Link to={userInfo ? '/menu' : '/'} className="logo">
          Arellano Canteen
        </Link>
 
        <nav>
          <ul>
            {userInfo ? (
              /* ── LOGGED IN ──────────────────────── */
              <>
                <li>
                  <Link to="/menu">Menu</Link>
                </li>
                <li>
                  <Link to="/cart">
                    <FaShoppingCart />
                    {cartCount > 0 && (
                      <span className="cart-badge">{cartCount}</span>
                    )}
                  </Link>
                </li>
                <li>
                  <Link to="/orders">
                    <FaClipboardList /> My Orders
                  </Link>
                </li>
                {userInfo.role === 'admin' && (
                  <li>
                    <Link to="/admin">Admin</Link>
                  </li>
                )}
                <li>
                  <button onClick={logoutHandler} className="logout-btn">
                    <FaSignOutAlt /> Logout
                  </button>
                </li>
              </>
            ) : (
              /* ── NOT LOGGED IN ──────────────────── */
              <>
                <li><Link to="/menu">Menu</Link></li>
                <li><Link to="/login">Login</Link></li>
                <li><Link to="/register">Register</Link></li>
              </>
            )}
          </ul>
        </nav>
      </div>
    </header>
  );
};
 
export default Header;