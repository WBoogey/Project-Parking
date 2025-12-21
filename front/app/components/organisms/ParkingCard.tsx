import Button from "../atoms/Button";
import SpotsStatus from "../atoms/SpotsStatus";
import { cn } from "cn-utility";

interface ParkingCardProps {
  name: string;
  totalSpots: number;
  availableSpots?: number;
  price?: string;
  onEdit?: () => void;
  onDelete?: () => void;
  className?: string;
  editLabel?: string;
}

const ParkingCard = ({
  name,
  totalSpots,
  availableSpots,
  price,
  onEdit,
  onDelete,
  className,
  editLabel = "Editer",
}: ParkingCardProps) => {
  return (
    <div
      data-testid="parking-card"
      className={cn(
        "w-full flex items-center justify-between p-5 border border-tertiary rounded-3xl bg-primary",
        className,
      )}
    >
      <div className="flex flex-col gap-1">
        <h3 className="text-secondary font-medium font-inter">{name}</h3>
        <div>
          <p className="text-tertiary font-medium font-inter">
            Nombre de places : {totalSpots}
          </p>
          {typeof availableSpots === "number" && (
            <SpotsStatus capacity={availableSpots} />
          )}
        </div>
      </div>
      <div className="flex items-center gap-4">
        {price && (
          <span className="text-secondary font-bold whitespace-nowrap">
            {price}
          </span>
        )}
        <div className="flex gap-2">
          {onEdit && (
            <Button onClick={onEdit} size="sm">
              {editLabel}
            </Button>
          )}
          {onDelete && (
            <Button
              onClick={onDelete}
              size="sm"
              variant="outline"
              className="text-red-500 border-red-500 hover:bg-red-50"
            >
              Supprimer
            </Button>
          )}
        </div>
      </div>
    </div>
  );
};

export default ParkingCard;

