import { useState, useEffect } from 'react';
import { useDispatch } from 'react-redux';
import { createFood, updateFood } from '../../redux/slices/foodSlice';
 
const FoodForm = ({ food, onClose }) => {
  const dispatch = useDispatch();
 
  const [formData, setFormData] = useState({
    name:        '',
    description: '',
    price:       '',
    category:    'Meal',
    image:       '',
    isAvailable: true,
  });
 
  useEffect(() => {
    if (food) {
      setFormData({
        name:        food.name        ?? '',
        description: food.description ?? '',
        price:       food.price       ?? '',
        category:    food.category    ?? 'Meal',
        image:       food.image       ?? '',
        isAvailable: food.is_available ?? food.isAvailable ?? true,
      });
    }
  }, [food]);
 
  const handleChange = (e) => {
    const { name, value, type, checked } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: type === 'checkbox' ? checked : value,
    }));
  };
 
  const handleSubmit = (e) => {
    e.preventDefault();
    if (food) {
      dispatch(updateFood({ id: food.id ?? food._id, foodData: formData }));
    } else {
      dispatch(createFood(formData));
    }
    onClose();
  };
 
  return (
    <div className="modal">
      <div className="modal-content">
 
        <div className="modal-header">
          <h2>{food ? 'Edit Food Item' : 'Add New Food Item'}</h2>
          <button className="modal-close" type="button" onClick={onClose}>✕</button>
        </div>
 
        <div className="modal-body">
          <form onSubmit={handleSubmit}>
 
            <div className="form-group">
              <label>Name</label>
              <input
                type="text"
                name="name"
                value={formData.name}
                onChange={handleChange}
                placeholder="e.g. Chicken Rice"
                required
              />
            </div>
 
            <div className="form-group">
              <label>Description</label>
              <textarea
                name="description"
                value={formData.description}
                onChange={handleChange}
                placeholder="Short description..."
              />
            </div>
 
            <div className="form-group">
              <label>Price (₱)</label>
              <input
                type="number"
                step="0.01"
                name="price"
                value={formData.price}
                onChange={handleChange}
                placeholder="0.00"
                required
              />
            </div>
 
            <div className="form-group">
              <label>Category</label>
              <select name="category" value={formData.category} onChange={handleChange}>
                <option value="Meal">Meal</option>
                <option value="Snack">Snack</option>
                <option value="Beverage">Beverage</option>
                <option value="Dessert">Dessert</option>
              </select>
            </div>
 
            <div className="form-group">
              <label>Image URL</label>
              <input
                type="text"
                name="image"
                value={formData.image}
                onChange={handleChange}
                placeholder="https://..."
              />
            </div>
 
            <div className="form-group">
              <label className="checkbox-label">
                <input
                  type="checkbox"
                  name="isAvailable"
                  checked={formData.isAvailable}
                  onChange={handleChange}
                />
                Mark as Available
              </label>
            </div>
 
            <div className="form-actions">
              <button type="button" className="btn-secondary" onClick={onClose}>
                Cancel
              </button>
              <button type="submit" className="btn-primary">
                {food ? 'Save Changes' : 'Add Food'}
              </button>
            </div>
 
          </form>
        </div>
      </div>
    </div>
  );
};
 
export default FoodForm;
