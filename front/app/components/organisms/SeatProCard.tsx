import { cn } from "cn-utility";
import type { SeatProCardData } from "@/types/SeatProCardTypes";

interface SeatProCardProps extends SeatProCardData {
  onClick?: () => void;
  className?: string;
}

const SeatProCard = ({
  name,
  status,
  limitReached = false,
  occupiedBy,
  reservationType,
  timeRange,
  onClick,
  className,
}: SeatProCardProps) => {
  const getBackgroundColor = () => {
    if (limitReached) return "bg-no-availability/60";
    if (status === "free") return "bg-available/60";
    return "bg-low-availability/60";
  };

  const getReservationLabel = () => {
    if (reservationType === "daily") return "Journée :";
    if (reservationType === "monthly") return "Mensuel :";
    return null;
  };

  const getTimeRangeDisplay = () => {
    if (!timeRange) return null;
    return `${timeRange.start} - ${timeRange.end}`;
  };

  return (
    <button
      type="button"
      onClick={onClick}
      className={cn(
        "w-full flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer",
        getBackgroundColor(),
        className,
      )}
    >
      <div className="flex items-center gap-6">
        <span className="text-secondary font-semibold">{name}</span>
        {status === "occupied" && occupiedBy && (
          <span className="text-secondary">
            Occupée par <span className="font-semibold">{occupiedBy}</span>
          </span>
        )}
      </div>

      <div className="flex items-center gap-6">
        {limitReached && (
          <span className="text-secondary font-medium">Limite atteinte</span>
        )}

        {status === "free" ? (
          <span className="text-secondary font-semibold">Libre</span>
        ) : (
          reservationType &&
          timeRange && (
            <div className="flex items-center gap-2">
              <span className="text-secondary">{getReservationLabel()}</span>
              <span className="text-secondary font-medium">
                {getTimeRangeDisplay()}
              </span>
            </div>
          )
        )}
      </div>
    </button>
  );
};

export default SeatProCard;
