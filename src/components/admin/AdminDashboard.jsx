import { Routes, Route, Link, useLocation, useNavigate } from 'react-router-dom';
import { useDispatch, useSelector } from 'react-redux';
import { toast } from 'react-toastify';
import { logout } from '../../redux/slices/authSlice';
import ManageFood from './ManageFood';
import ManageOrders from './ManageOrders';
import DailyOrders from './DailyOrders';
import '../../assets/css/AdminDashboard.css';
import logo from '../../assets/images/AU_Logo.png';
 
const AdminDashboard = () => {
  const location     = useLocation();
  const navigate     = useNavigate();
  const dispatch     = useDispatch();
  const { userInfo } = useSelector((state) => state.auth);
 
  const isActive = (path) =>
    location.pathname === path || location.pathname.startsWith(path + '/');
 
  const handleLogout = () => {
    if (window.confirm('Are you sure you want to sign out?')) {
      dispatch(logout());
      toast.success('Logged out successfully');
      navigate('/login');
    }
  };
 
  const getInitials = (name) => {
    if (!name) return 'AD';
    return name.split(' ').map((n) => n[0]).join('').toUpperCase().slice(0, 2);
  };
 
  const pageTitle = () => {
    if (location.pathname === '/admin')         return 'Dashboard Overview';
    if (isActive('/admin/foods'))               return 'Menu Curator';
    if (isActive('/admin/orders'))              return 'Order Management';
    if (isActive('/admin/daily'))               return 'Daily Summary';
    return 'Admin';
  };
 
  return (
    <div className="admin-dashboard">
 
      {/* ── SIDEBAR (Editorial Style) ─────────────────────────── */}
      <aside className="sidebar">
        <div>
          <div className="sidebar-brand">
            <div className="sidebar-brand-icon">
              <img src={logo} alt="AU Logo" style={{ width: '100%', height: '100%', objectFit: 'contain' }} />
            </div>
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
        </div>

        {/* LOGOUT AT THE BOTTOM */}
        <div className="sidebar-footer">
          <button className="sidebar-logout-btn" onClick={handleLogout}>
            <span className="nav-icon">⏻</span> Sign Out
          </button>
        </div>
      </aside>
 
      {/* ── MAIN ────────────────────────────── */}
      <div className="admin-main">
 
        {/* TOPBAR (Simplified) */}
        <header className="admin-topbar">
          <div className="topbar-page-title">{pageTitle()}</div>
 
          <div className="topbar-user-context">
            <div className="topbar-user-info">
              <strong>{userInfo?.name || 'Admin'}</strong>
              <small>{userInfo?.role || 'Administrator'}</small>
            </div>
            <div className="topbar-avatar">
              {getInitials(userInfo?.name)}
            </div>
          </div>
        </header>
 
        {/* CONTENT */}
        <main className="admin-content">
          <Routes>
            <Route
              path="/"
              element={
                <div>
                  <div className="dashboard-welcome">
                    <h1>Welcome back, {userInfo?.name?.split(' ')[0] || 'Admin'}</h1>
                    <p>Review the university canteen performance and live metrics.</p>
                  </div>

                  <div className="stats-grid">
                    <div className="stat-card dark">
                      <div className="stat-card-label">Live Orders</div>
                      <div className="stat-card-value">—</div>
                      <div className="stat-card-sub">Active in kitchen</div>
                    </div>
                    <div className="stat-card">
                      <div className="stat-card-label">Revenue Today</div>
                      <div className="stat-card-value">₱ —</div>
                      <div className="stat-card-sub">Net earnings</div>
                    </div>
                    <div className="stat-card">
                      <div className="stat-card-label">Catalog</div>
                      <div className="stat-card-value">—</div>
                      <div className="stat-card-sub">Items listed</div>
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