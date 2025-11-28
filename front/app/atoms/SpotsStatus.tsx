import { cn } from "cn-utility";

interface SpotsStatus {
  capacity: number;
  className?: string;
}

const SpotsStatus = ({ capacity, className }: SpotsStatus) => {
  const message =
    capacity > 0
      ? `${capacity} ${capacity === 1 ? "place disponible" : "places disponibles"}`
      : "Complet";

  return (
    <div
      className={cn(
        capacity > 0 ? "text-low-availability" : "text-no-availability",
        className,
      )}
    >
      {message}
    </div>
  );
};

export default SpotsStatus;
