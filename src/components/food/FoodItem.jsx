import { useDispatch, useSelector } from 'react-redux';
import { useNavigate } from 'react-router-dom';
import { addToCart } from '../../redux/slices/cartSlice';
import { toast } from 'react-toastify';
 
const CATEGORY_EMOJI = {
  meal:     '🍱',
  snack:    '🍟',
  beverage: '🧃',
  dessert:  '🍮',
};
 
const getEmoji = (category = '') =>
  CATEGORY_EMOJI[category.toLowerCase()] ?? '🍽️';
 
const FoodItem = ({ food, style }) => {
  const dispatch     = useDispatch();
  const navigate     = useNavigate();
  const { userInfo } = useSelector((state) => state.auth);
 
  const isAvailable = food.is_available ?? food.isAvailable ?? true;
  const foodId      = food.id ?? food._id;
 
  const addToCartHandler = () => {
    // Guard: require login before adding to cart
    if (!userInfo) {
      toast.info('Please sign in to add items to your cart 🔐');
      navigate('/login');
      return;
    }
    dispatch(addToCart({ foodId, quantity: 1 }));
    toast.success(`${food.name} added to cart! 🛒`);
  };
 
  return (
    <div
      className={`food-card ${!isAvailable ? 'food-card--unavailable' : ''}`}
      style={style}
    >
 
      {/* Image */}
      <div className="food-card-img-wrap">
        {food.image ? (
          <img src={food.image} alt={food.name} loading="lazy" />
        ) : (
          <div className="food-card-img-placeholder">
            {getEmoji(food.category)}
          </div>
        )}
        <span className={`food-card-badge ${!isAvailable ? 'unavailable' : ''}`}>
          {isAvailable ? 'Available' : 'Sold Out'}
        </span>
      </div>
 
      {/* Body */}
      <div className="food-card-body">
        {food.category && (
          <span className="food-card-category">
            {getEmoji(food.category)} {food.category}
          </span>
        )}
        <h3 className="food-card-name">{food.name}</h3>
        {food.description && (
          <p className="food-card-desc">{food.description}</p>
        )}
 
        <div className="food-card-footer">
          <div className="food-card-price">
            ₱{Number(food.price).toFixed(2)}
          </div>
          <button
            className="food-card-btn"
            onClick={addToCartHandler}
            disabled={!isAvailable}
          >
            {isAvailable ? '+ Add' : 'Sold Out'}
          </button>
        </div>
      </div>
 
    </div>
  );
};
 
export default FoodItem;