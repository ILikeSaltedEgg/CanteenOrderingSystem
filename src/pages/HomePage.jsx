import { Link, Navigate } from 'react-router-dom';
import { useSelector } from 'react-redux';
import { useEffect, useRef } from 'react';
import '../assets/css/HomePage.css';
 
const features = [
  { icon: '🍱', title: 'Fresh Daily',    desc: 'Meals prepared fresh every morning by our canteen staff.' },
  { icon: '⚡', title: 'Skip the Queue', desc: "Order ahead and pick up when it's ready — no waiting." },
  { icon: '💳', title: 'Easy Checkout',  desc: 'Simple, fast ordering from your phone or computer.' },
];
 
const categories = [
  { label: 'Rice Meals', emoji: '🍚' },
  { label: 'Snacks',     emoji: '🍟' },
  { label: 'Drinks',     emoji: '🧃' },
  { label: 'Desserts',   emoji: '🍮' },
];
 
const HomePage = () => {
  const { userInfo } = useSelector((state) => state.auth);
  const heroRef = useRef(null);
 
  useEffect(() => {
    const observer = new IntersectionObserver(
      (entries) => entries.forEach((e) => e.isIntersecting && e.target.classList.add('visible')),
      { threshold: 0.1 }
    );
    document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));
    return () => observer.disconnect();
  }, []);
 
  // Logged-in students skip the landing page and go straight to menu
  if (userInfo && userInfo.role !== 'admin') {
    return <Navigate to="/menu" replace />;
  }
 
  return (
    <div className="hp-wrapper">
 
      <section className="hp-hero" ref={heroRef}>
        <div className="hp-blob hp-blob-1" />
        <div className="hp-blob hp-blob-2" />
        <div className="hp-hero-inner">
          <span className="hp-badge">🎓 University Canteen</span>
          <h1 className="hp-title">
            Good food,<br />
            <span className="hp-title-accent">no waiting</span>
          </h1>
          <p className="hp-subtitle">
            Order your favorite campus meals online and pick them up fresh — skip the long lunch queues.
          </p>
          <div className="hp-cta-row">
            <Link to="/menu" className="hp-btn-dark">Browse Menu →</Link>
            <Link to="/register" className="hp-btn-ghost">Create Account</Link>
          </div>
        </div>
        <div className="hp-scroll-cue">scroll</div>
      </section>
 
     <div className="hp-section">
        <p className="hp-eyebrow reveal">What's available</p>
        <h2 className="hp-heading reveal">Browse by category</h2>
        <div className="hp-cats">
          {categories.map((cat, i) => (
            <Link
              to="/menu"
              className="hp-cat reveal"
              key={cat.label}
              style={{ transitionDelay: `${i * 0.08}s` }}
            >
              <span className="hp-cat-emoji">{cat.emoji}</span>
              <span className="hp-cat-name">{cat.label}</span>
            </Link>
          ))}
        </div>
      </div>
 
      <div className="hp-section" style={{ paddingTop: 0 }}>
        <p className="hp-eyebrow reveal">Why order online</p>
        <h2 className="hp-heading reveal">Built for busy students</h2>
        <div className="hp-feats">
          {features.map((f, i) => (
            <div
              className="hp-feat reveal"
              key={f.title}
              style={{ transitionDelay: `${i * 0.1}s` }}
            >
              <span className="hp-feat-icon">{f.icon}</span>
              <div className="hp-feat-title">{f.title}</div>
              <div className="hp-feat-desc">{f.desc}</div>
            </div>
          ))}
        </div>
      </div>
 
     <div className="hp-banner reveal">
        <h2>Hungry? Let's go. 🍴</h2>
        <p>Check today's menu and place your order in under a minute.</p>
        <Link to="/menu" className="hp-btn-amber">See Today's Menu →</Link>
      </div>
 
    </div>
  );
};
 
export default HomePage;
