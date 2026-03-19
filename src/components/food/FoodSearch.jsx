import { useState } from 'react';
 
const FoodSearch = ({ onSearch }) => {
  const [query, setQuery] = useState('');
 
  const handleChange = (e) => {
    setQuery(e.target.value);
    onSearch(e.target.value); // live search as user types
  };
 
  const handleSubmit = (e) => {
    e.preventDefault();
    onSearch(query);
  };
 
  return (
    <form onSubmit={handleSubmit} className="food-search">
      <span className="food-search-icon"></span>
      <input
        type="text"
        placeholder="Search meals, drinks..."
        value={query}
        onChange={handleChange}
      />
      <button type="submit" className="food-search-btn">Search</button>
    </form>
  );
};
 
export default FoodSearch;
 