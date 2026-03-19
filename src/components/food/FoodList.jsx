import { useEffect } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { fetchFoods } from '../../redux/slices/foodSlice';
import FoodItem from './FoodItem';
import Loader from '../common/Loader';
 
const FoodList = ({ filters }) => {
  const dispatch = useDispatch();
  const { foods, isLoading } = useSelector((state) => state.food);
 
  useEffect(() => {
    dispatch(fetchFoods(filters));
  }, [dispatch, filters]);
 
  if (isLoading) return <Loader />;
 
  if (!foods || foods.length === 0) {
    return (
      <div className="food-list-empty">
        <span className="food-list-empty-icon">🍽️</span>
        <h3>Nothing here yet</h3>
        <p>Try a different category or search term.</p>
      </div>
    );
  }
 
  return (
    <div className="food-list">
      {foods.map((food, i) => (
        <FoodItem
          key={food.id ?? food._id}
          food={food}
          style={{ animationDelay: `${i * 0.05}s` }}
        />
      ))}
    </div>
  );
};
 
export default FoodList;