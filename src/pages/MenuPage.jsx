import { useState } from 'react';
import FoodList from '../components/food/FoodList';
import FoodFilter from '../components/food/FoodFilter';
import FoodSearch from '../components/food/FoodSearch';
import '../assets/css/Menu.css';
 
const MenuPage = () => {
  const [filters, setFilters]         = useState({ category: '', availableOnly: false });
  const [searchQuery, setSearchQuery] = useState('');
 
  const handleFilterChange = (newFilters) => setFilters(newFilters);
  const handleSearch       = (query)      => setSearchQuery(query);
 
  const queryParams = { ...filters, search: searchQuery };
 
  return (
    <div className="mp-wrapper">
 
      {/* ── PAGE HEADER ── */}
      <div className="mp-header">
        <p className="mp-header-eyebrow">What's cooking today</p>
        <h1 className="mp-header-title">Our Menu</h1>
        <p className="mp-header-sub">Fresh selections updated daily by the canteen</p>
      </div>
 
      {/* ── TOOLBAR ── */}
      <div className="mp-toolbar">
        <FoodSearch onSearch={handleSearch} />
        <FoodFilter onFilterChange={handleFilterChange} />
      </div>
 
      {/* ── FOOD GRID ── */}
      <div className="mp-content">
        <FoodList filters={queryParams} />
      </div>
 
    </div>
  );
};
 
export default MenuPage;