import { Link, Navigate } from 'react-router-dom';
import { useSelector } from 'react-redux';
import { useEffect, useRef } from 'react';
import '../assets/css/HomePage.css';
import Signature from '../assets/images/Signature.png';

 
const features = [
  { icon: '⚡', title: 'Skip the Line',   desc: 'Order 15 minutes before your class ends and pick it up at the priority window.' },
  { icon: '🥗', title: 'Nutrition First', desc: 'Full macro-nutrient breakdowns for every meal, synced with campus wellness apps.' },
  { icon: '💳', title: 'Student Credit',  desc: 'Seamless integration with your University Student Card and campus meal plans.' },
  { icon: '🔔', title: 'Live Status',     desc: 'Real-time updates on kitchen capacity and estimated preparation times.' },
];
 
const categories = [
  { label: 'Power Breakfast', emoji: '🍳', desc: 'Start your lecture circuit with sustained energy and fresh ingredients.' },
  { label: 'Hot Entrées',     emoji: '🍽', desc: 'Chef-prepared gourmet meals served daily.' },
  { label: 'Artisan Coffee',  emoji: '☕', desc: 'Ethically sourced beans.' },
  { label: 'Vegan Select',    emoji: '🌿', desc: '100% plant-based fuel.' },
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
 
  // ── EARLY RETURN after all hooks ──────────────────────────
  if (userInfo && userInfo.role !== 'admin') {
    return <Navigate to="/menu" replace />;
  }
 
  return (
    <div className="hp-wrapper">
 
      {/* ── HERO ── */}
      <section className="hp-hero" ref={heroRef}>
        <div className="hp-hero-left">
          <h1 className="hp-title">
            Good food,<br />
            <span className="hp-title-accent">no waiting.</span>
          </h1>
          <p className="hp-subtitle">
            Experience university dining redefined. Curated menus, precise nutritional data,
            and zero-friction ordering for the modern academic schedule.
          </p>
          <div className="hp-cta-row">
            <Link to="/menu" className="hp-btn-primary">Browse Menu</Link>
            <Link to="/menu" className="hp-btn-ghost">View Specials</Link>
          </div>
        </div>
 
        <div className="hp-hero-right">
          <div className="hp-hero-img-wrap">
            {/* Replace the emoji span below with <img src="…" alt="…" /> for a real food photo */}
            <div className="hp-hero-img-placeholder"><span><img src={Signature} alt="Daily Signature Dish" /></span></div>
            <div className="hp-hero-card">
              <span className="hp-hero-card-label">Daily Signature</span>
              <strong>Harvest Grain &amp; Chicken Bowl</strong>
              <span className="hp-hero-card-meta">420 kcal · High Protein</span>
            </div>
          </div>
        </div>
      </section>
 
      {/* ── CATEGORIES ── */}
      <div className="hp-section">
        <div className="hp-section-header reveal">
          <div>
            <h2 className="hp-heading">Browse by category</h2>
            <p className="hp-section-sub">Find exactly what you need to fuel your study sessions.</p>
          </div>
          <Link to="/menu" className="hp-see-all">See All Categories →</Link>
        </div>
 
        <div className="hp-cats-editorial">
          {/* Large featured card */}
          <Link to="/menu" className="hp-cat-featured reveal">
            <span className="hp-cat-feat-emoji">{categories[0].emoji}</span>
            <strong className="hp-cat-feat-name">{categories[0].label}</strong>
            <p className="hp-cat-feat-desc">{categories[0].desc}</p>
            <span className="hp-cat-explore">Explore →</span>
          </Link>
 
          {/* Right column */}
          <div className="hp-cats-right">
            <Link to="/menu" className="hp-cat-wide reveal">
              <div>
                <span className="hp-cat-emoji">{categories[1].emoji}</span>
                <strong className="hp-cat-name">{categories[1].label}</strong>
                <p className="hp-cat-desc">{categories[1].desc}</p>
              </div>
              <div className="hp-cat-img-placeholder">🍽</div>
            </Link>
 
            <div className="hp-cats-small-row">
              {categories.slice(2).map((cat) => (
                <Link to="/menu" className="hp-cat-small reveal" key={cat.label}>
                  <span className="hp-cat-emoji">{cat.emoji}</span>
                  <strong className="hp-cat-name">{cat.label}</strong>
                  <p className="hp-cat-desc">{cat.desc}</p>
                </Link>
              ))}
            </div>
          </div>
        </div>
      </div>
 
      {/* ── FEATURES ── */}
      <div className="hp-feats-section">
        <div className="hp-feats-grid">
          <div className="hp-feats-left">
            {features.map((f, i) => (
              <div
                className="hp-feat reveal"
                key={f.title}
                style={{ transitionDelay: `${i * 0.1}s` }}
              >
                <span className="hp-feat-icon">{f.icon}</span>
                <div>
                  <div className="hp-feat-title">{f.title}</div>
                  <div className="hp-feat-desc">{f.desc}</div>
                </div>
              </div>
            ))}
          </div>
 
          <div className="hp-feats-right reveal">
            <h2 className="hp-heading" style={{ marginBottom: '1rem' }}>
              Built for busy students.
            </h2>
            <p className="hp-feat-summary">
              We understand that your time is as valuable as your health. Our platform is designed
              to seamlessly integrate food into your academic workflow.
            </p>
            <div className="hp-testimonial">
              <div className="hp-testimonial-avatar">👤</div>
              <div>
                <strong>Alex Rivera</strong>
                <span>Medical Student</span>
                <p>
                  "The ability to track my macros and order between labs has literally saved my diet
                  this semester. It's more like a premium concierge than a canteen."
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
 
      {/* ── CTA BANNER ── */}
      <div className="hp-banner reveal">
        <h2>Ready to upgrade your lunch?</h2>
        <p>
          Join over 15,000 students and faculty members who use Editorial daily for a better
          dining experience.
        </p>
        <div className="hp-banner-btns">
          <Link to="/register" className="hp-btn-ghost hp-btn-ghost-inv">Get Started Now</Link>
          <Link to="/menu"     className="hp-btn-primary hp-btn-primary-light">Browse Menu →</Link>
        </div>
      </div>
 
    </div>
  );
};
 
export default HomePage;