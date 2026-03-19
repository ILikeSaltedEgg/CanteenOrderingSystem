import { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { fetchFoods, deleteFood } from '../../redux/slices/foodSlice';
import FoodForm from './FoodForm';
import Loader from '../common/Loader';
import { FaEdit, FaTrash } from 'react-icons/fa';
 
const ManageFood = () => {
  const dispatch = useDispatch();
  const { foods, isLoading } = useSelector((state) => state.food);
  const [showForm, setShowForm]     = useState(false);
  const [editingFood, setEditingFood] = useState(null);
 
  useEffect(() => {
    dispatch(fetchFoods());
  }, [dispatch]);
 
  const handleEdit = (food) => {
    setEditingFood(food);
    setShowForm(true);
  };
 
  const handleDelete = (id) => {
    if (window.confirm('Are you sure you want to delete this item?')) {
      dispatch(deleteFood(id));
    }
  };
 
  const handleCloseForm = () => {
    setShowForm(false);
    setEditingFood(null);
  };
 
  if (isLoading) return <Loader />;
 
  return (
    <div>
      <div className="page-header">
        <div>
          <h2>Manage Food Items</h2>
          <p>{foods.length} items in menu</p>
        </div>
        <button className="btn-primary" onClick={() => setShowForm(true)}>
          + Add New Food
        </button>
      </div>
 
      {showForm && <FoodForm food={editingFood} onClose={handleCloseForm} />}
 
      <div className="panel">
        <div className="panel-header">
          <span className="panel-title">Food Items</span>
        </div>
        <div className="table-wrapper">
          <table>
            <thead>
              <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Available</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              {foods.length === 0 ? (
                <tr>
                  <td colSpan="6">
                    <div className="empty-state">
                      <p>No food items found. Add one to get started.</p>
                    </div>
                  </td>
                </tr>
              ) : (
                foods.map((food) => {
                  // Supabase uses `id`, not `_id`
                  const foodId        = food.id ?? food._id;
                  const isAvailable   = food.is_available ?? food.isAvailable;
 
                  return (
                    <tr key={foodId}>
                      <td>
                        <img src={food.image || '/default.jpg'} alt={food.name} />
                      </td>
                      <td><strong>{food.name}</strong></td>
                      <td>{food.category}</td>
                      <td className="td-mono">₱{food.price}</td>
                      <td>
                        <span className={`badge ${isAvailable ? 'badge-available' : 'badge-unavailable'}`}>
                          {isAvailable ? 'Yes' : 'No'}
                        </span>
                      </td>
                      <td>
                        <div className="td-actions">
                          <button
                            className="btn-icon"
                            onClick={() => handleEdit(food)}
                            title="Edit"
                          >
                            <FaEdit />
                          </button>
                          <button
                            className="btn-icon btn-danger"
                            onClick={() => handleDelete(foodId)}
                            title="Delete"
                          >
                            <FaTrash />
                          </button>
                        </div>
                      </td>
                    </tr>
                  );
                })
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};
 
export default ManageFood;