const STATUS_STEPS = ['Pending', 'Preparing', 'Ready for Pickup', 'Completed'];
 
const STATUS_ICONS = {
  'Pending':          '🕐',
  'Preparing':        '👨‍🍳',
  'Ready for Pickup': '✅',
  'Completed':        '🎉',
};
 
const OrderStatus = ({ status }) => {
  const currentIndex = STATUS_STEPS.indexOf(status);
 
  return (
    <div className="os-tracker">
      {STATUS_STEPS.map((step, index) => {
        const isDone    = index < currentIndex;
        const isCurrent = step === status;
 
        return (
          <div key={step} className="os-step-wrap">
            <div className={`os-step ${isDone ? 'done' : ''} ${isCurrent ? 'current' : ''}`}>
              <div className="os-dot">
                {isDone    ? '✓'                : ''}
                {isCurrent ? STATUS_ICONS[step] : ''}
                {!isDone && !isCurrent ? (index + 1) : ''}
              </div>
              <span className="os-step-label">{step}</span>
            </div>
            {index < STATUS_STEPS.length - 1 && (
              <div className={`os-connector ${isDone ? 'done' : ''}`} />
            )}
          </div>
        );
      })}
    </div>
  );
};
 
export default OrderStatus;