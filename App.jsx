import { Routes, Route, useLocation } from 'react-router-dom';
import Header from './components/common/Header';
import Footer from './components/common/Footer';
import PrivateRoute from './components/common/PrivateRoute';
import HomePage from './pages/HomePage';
import MenuPage from './pages/MenuPage';
import LoginPage from './pages/LoginPage';
import RegisterPage from './pages/RegisterPage';
import CartPage from './pages/CartPage';
import CheckoutPage from './pages/CheckoutPage';
import OrderHistoryPage from './pages/OrderHistoryPage';
import AdminDashboardPage from './pages/AdminDashboardPage';
import NotFoundPage from './pages/NotFoundPage';
 
function App() {
  const location = useLocation();
  const isAdminRoute = location.pathname.startsWith('/admin');
  const isAuthRoute = ['/login', '/register'].includes(location.pathname);

  const hideLayout = isAdminRoute || isAuthRoute;
 
  return (
    <div className="app">
      {!hideLayout && <Header />}
 
      <main className={isAdminRoute ? '' : 'main-content'}>
        <Routes>
          <Route path="/"         element={<HomePage />} />
          <Route path="/menu"     element={<MenuPage />} />
          <Route path="/login"    element={<LoginPage />} />
          <Route path="/register" element={<RegisterPage />} />
          <Route path="/cart"     element={<PrivateRoute><CartPage /></PrivateRoute>} />
          <Route path="/checkout" element={<PrivateRoute><CheckoutPage /></PrivateRoute>} />
          <Route path="/orders"   element={<PrivateRoute><OrderHistoryPage /></PrivateRoute>} />
          <Route path="/admin/*"  element={<AdminDashboardPage />} />
          <Route path="*"         element={<NotFoundPage />} />
        </Routes>
      </main>

        
      {!hideLayout && <Footer />}
    </div>
  );
}
 
export default App;