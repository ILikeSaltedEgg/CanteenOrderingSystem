import { useSelector } from 'react-redux';
import { Navigate } from 'react-router-dom';
import AdminDashboard from '../components/admin/AdminDashboard';
 
/**
 * AdminDashboardPage
 * - Wraps AdminDashboard with a role check.
 * - If not logged in → redirect to /login
 * - If logged in but NOT admin → redirect to / (home)
 * - No Header or Footer rendered here; AdminDashboard has its own full-page layout.
 */
const AdminDashboardPage = () => {
  const { userInfo } = useSelector((state) => state.auth);
 
  if (!userInfo) {
    return <Navigate to="/login" replace />;
  }
 
  if (userInfo.role !== 'admin') {
    return <Navigate to="/" replace />;
  }
 
  return <AdminDashboard />;
};
 
export default AdminDashboardPage;