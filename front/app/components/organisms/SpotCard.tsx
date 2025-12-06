import PriceIndicator from "../atoms/PriceIndicator";
import { cn } from "cn-utility";

interface SpotCardProps {
  name: string;
  address: string;
  price: number;
  capacity: number;
  imageUrl?: string;
  onClick: () => void;
}

const SpotCard = ({
  name,
  address,
  price,
  capacity,
  imageUrl,
  onClick,
}: SpotCardProps) => {
  const isAvailable = capacity >= 1;

  return (
    <button
      onClick={onClick}
      data-testid="spot-card"
      className={cn(
        "flex gap-7 border border-tertiary rounded-lg cursor-pointer text-left overflow-hidden w-176 h-46",
        {
          "bg-primary": isAvailable,
        },
      )}
    >
      <div className="h-full aspect-square shrink-0">
        {imageUrl ? (
          <img
            src={imageUrl}
            alt={name}
            className="aspect-square object-cover shrink-0 size-full"
          />
        ) : (
          <div className="bg-zinc-700 size-full" />
        )}
      </div>
      <div className="flex flex-col justify-center gap-8">
        <div>
          <h3
            className={cn(
              "font-inter font-semibold",
              isAvailable ? "text-secondary" : "text-accent",
            )}
          >
            {name}
          </h3>
          <p className="text-tertiary text-sm font-semibold">{address}</p>
        </div>
        <PriceIndicator price={price} variant="inline" />
      </div>
    </button>
  );
};

export default SpotCard;
