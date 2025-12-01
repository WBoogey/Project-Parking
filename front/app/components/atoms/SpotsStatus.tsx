import { cn } from "cn-utility";

interface SpotsStatusProps {
  capacity: number;
  className?: string;
}

const SpotsStatus = ({ capacity, className }: SpotsStatusProps) => {
  const message =
    capacity > 0
      ? `${capacity} ${capacity === 1 ? "place disponible" : "places disponibles"}`
      : "Complet";

  return (
    <div
      className={cn(
        "font-semibold",
        capacity > 0 ? "text-low-availability" : "text-no-availability",
        className,
      )}
    >
      {message}
    </div>
  );
};

export default SpotsStatus;
