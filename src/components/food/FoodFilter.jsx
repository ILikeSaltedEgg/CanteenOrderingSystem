import { useState } from 'react';
 
const CATEGORIES = [
  { value: '',         label: 'All Categories', emoji: '🍽️' },
  { value: 'Meal',     label: 'Meals',          emoji: '🍱' },
  { value: 'Snack',    label: 'Snacks',         emoji: '🍟' },
  { value: 'Beverage', label: 'Beverages',      emoji: '🧃' },
  { value: 'Dessert',  label: 'Desserts',       emoji: '🍮' },
];
 
const FoodFilter = ({ onFilterChange }) => {
  const [category, setCategory]         = useState('');
  const [availableOnly, setAvailableOnly] = useState(false);
 
  const handleCategory = (value) => {
    setCategory(value);
    onFilterChange({ category: value, availableOnly });
  };
 
  const handleAvailable = (e) => {
    setAvailableOnly(e.target.checked);
    onFilterChange({ category, availableOnly: e.target.checked });
  };
 
  return (
    <div className="food-filter">
      {/* Category pill buttons */}
      <div className="food-filter-tabs">
        {CATEGORIES.map((cat) => (
          <button
            key={cat.value}
            type="button"
            className={`food-filter-btn ${category === cat.value ? 'active' : ''}`}
            onClick={() => handleCategory(cat.value)}
          >
            <span>{cat.emoji}</span> {cat.label}
          </button>
        ))}
      </div>
 
      {/* Available only toggle */}
      <label className="food-filter-toggle">
        <input
          type="checkbox"
          checked={availableOnly}
          onChange={handleAvailable}
        />
        <span>Available only</span>
      </label>
    </div>
  );
};
 
export default FoodFilter;