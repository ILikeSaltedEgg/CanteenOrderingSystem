import { useState, useRef, useEffect } from 'react';
import { Routes, Route, Link, useLocation, useNavigate } from 'react-router-dom';
import { useDispatch, useSelector } from 'react-redux';
import { toast } from 'react-toastify';
import { logout } from '../../redux/slices/authSlice';
import ManageFood from './ManageFood';
import ManageOrders from './ManageOrders';
import DailyOrders from './DailyOrders';
import '../../assets/css/AdminDashboard.css';
 
const AdminDashboard = () => {
  const location     = useLocation();
  const navigate     = useNavigate();
  const dispatch     = useDispatch();
  const { userInfo } = useSelector((state) => state.auth);
 
  const [dropdownOpen, setDropdownOpen] = useState(false);
  const dropdownRef = useRef(null);

  useEffect(() => {
    const handleClickOutside = (e) => {
      if (dropdownRef.current && !dropdownRef.current.contains(e.target)) {
        setDropdownOpen(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);
 
  const isActive = (path) =>
    location.pathname === path || location.pathname.startsWith(path + '/');
 
  const handleLogout = () => {
    dispatch(logout());
    toast.success('Logged out successfully');
    navigate('/login');
  };
 
  const getInitials = (name) => {
    if (!name) return 'AD';
    return name.split(' ').map((n) => n[0]).join('').toUpperCase().slice(0, 2);
  };
 
  const pageTitle = () => {
    if (location.pathname === '/admin')         return 'Dashboard';
    if (isActive('/admin/foods'))               return 'Manage Food';
    if (isActive('/admin/orders'))              return 'Manage Orders';
    if (isActive('/admin/daily'))               return 'Daily Orders';
    return 'Admin';
  };
 
  return (
    <div className="admin-dashboard">

      <aside className="sidebar">
        <div className="sidebar-brand">
          <div className="sidebar-brand-icon">🍽️</div>
          <div className="sidebar-brand-text">
            <strong>Canteen</strong>
            <small>Admin Panel</small>
          </div>
        </div>
 
        <span className="sidebar-section-label">Navigation</span>
        <ul>
          <li>
            <Link to="/admin" className={location.pathname === '/admin' ? 'active' : ''}>
              <span className="nav-icon">⊞</span> Dashboard
            </Link>
          </li>
          <li>
            <Link to="/admin/foods" className={isActive('/admin/foods') ? 'active' : ''}>
              <span className="nav-icon">🥗</span> Manage Food
            </Link>
          </li>
          <li>
            <Link to="/admin/orders" className={isActive('/admin/orders') ? 'active' : ''}>
              <span className="nav-icon">📋</span> Manage Orders
            </Link>
          </li>
          <li>
            <Link to="/admin/daily" className={isActive('/admin/daily') ? 'active' : ''}>
              <span className="nav-icon">📊</span> Daily Orders
            </Link>
          </li>
        </ul>
      </aside>

      <div className="admin-main">
 
        <header className="admin-topbar">
          <div className="topbar-page-title">{pageTitle()}</div>
 
          <div className="topbar-actions">
 
            <div className="topbar-user-wrap" ref={dropdownRef}>
              <div
                className={`topbar-user ${dropdownOpen ? 'open' : ''}`}
                onClick={() => setDropdownOpen((p) => !p)}
                role="button"
                tabIndex={0}
                onKeyDown={(e) => e.key === 'Enter' && setDropdownOpen((p) => !p)}
              >
                <div className="topbar-avatar">
                  {getInitials(userInfo?.name)}
                </div>
                <div className="topbar-user-info">
                  <strong>{userInfo?.name || 'Admin'}</strong>
                  <small>{userInfo?.email || 'Administrator'}</small>
                </div>
                <span className="topbar-chevron">{dropdownOpen ? '▲' : '▼'}</span>
              </div>
 
              {dropdownOpen && (
                <div className="topbar-dropdown">
                  <div className="topbar-dropdown-header">
                    <div className="topbar-dropdown-avatar">
                      {getInitials(userInfo?.name)}
                    </div>
                    <div className="topbar-dropdown-info">
                      <strong>{userInfo?.name || 'Admin'}</strong>
                      <small>{userInfo?.email}</small>
                    </div>
                  </div>
                  <div className="topbar-dropdown-divider" />
                  <div className="topbar-dropdown-role">
                    <span className="badge badge-available">
                      {userInfo?.role || 'admin'}
                    </span>
                  </div>
                  <div className="topbar-dropdown-divider" />
                  <button className="topbar-dropdown-logout" onClick={handleLogout}>
                    <span>⏻</span> Sign Out
                  </button>
                </div>
              )}
            </div>
 
          </div>
        </header>
 
        <main className="admin-content">
          <Routes>
            <Route
              path="/"
              element={
                <div>
                  <div className="dashboard-welcome">
                    <h1>Good morning, {userInfo?.name?.split(' ')[0] || 'Admin'} 👋</h1>
                    <p>Here's what's happening in your canteen today.</p>
                  </div>
                  <div className="stats-grid">
                    <div className="stat-card dark">
                      <div className="stat-card-label">Today's Orders</div>
                      <div className="stat-card-value">—</div>
                      <div className="stat-card-sub">Live count</div>
                      <div className="stat-card-icon">📋</div>
                    </div>
                    <div className="stat-card">
                      <div className="stat-card-label">Revenue Today</div>
                      <div className="stat-card-value">—</div>
                      <div className="stat-card-sub">Updated live</div>
                      <div className="stat-card-icon">💰</div>
                    </div>
                    <div className="stat-card">
                      <div className="stat-card-label">Food Items</div>
                      <div className="stat-card-value">—</div>
                      <div className="stat-card-sub">In menu</div>
                      <div className="stat-card-icon">🥗</div>
                    </div>
                  </div>
                </div>
              }
            />
            <Route path="/foods"  element={<ManageFood />} />
            <Route path="/orders" element={<ManageOrders />} />
            <Route path="/daily"  element={<DailyOrders />} />
          </Routes>
        </main>
      </div>
    </div>
  );
};
 
export default AdminDashboard;
