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
        "w-full flex flex-col gap-4 p-5 border border-tertiary rounded-3xl bg-primary",
        className,
      )}
    >
      <h3 className="text-secondary font-semibold text-lg">{name}</h3>

      <div className="flex items-center justify-between">
        <p className="text-tertiary font-medium">
          Nombre de places : {totalSpots}
        </p>
        {typeof availableSpots === "number" && (
          <SpotsStatus capacity={availableSpots} />
        )}
        {price && (
          <span className="text-secondary font-bold whitespace-nowrap">
            {price}
          </span>
        )}
      </div>

      {(onEdit || onDelete) && (
        <div className="flex gap-3 w-full">
          {onEdit && (
            <Button onClick={onEdit} size="sm" className="flex-1">
              {editLabel}
            </Button>
          )}
          {onDelete && (
            <Button
              onClick={onDelete}
              size="sm"
              variant="outline"
              className="flex-1 text-red-500 border-red-500 hover:bg-red-50"
            >
              Supprimer
            </Button>
          )}
        </div>
      )}
    </div>
  );
};

export default ParkingCard;
