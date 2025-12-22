import { lazy, Suspense } from "react";

const ParkingMap = lazy(() => import("./ParkingMap"));

interface ParkingMapClientProps {
  address: string;
  className?: string;
}

export default function ParkingMapClient({
  address,
  className = "",
}: ParkingMapClientProps) {
  if (typeof window === "undefined") {
    return (
      <div
        className={`bg-gray-200 rounded-3xl flex items-center justify-center ${className}`}
      >
        <span className="text-gray-400">Carte</span>
      </div>
    );
  }

  return (
    <Suspense
      fallback={
        <div
          className={`bg-gray-200 rounded-3xl flex items-center justify-center ${className}`}
        >
          <div className="flex flex-col items-center gap-3">
            <div className="w-8 h-8 border-3 border-gray-300 border-t-secondary rounded-full animate-spin" />
            <span className="text-gray-500 text-sm">
              Chargement de la carte...
            </span>
          </div>
        </div>
      }
    >
      <ParkingMap address={address} className={className} />
    </Suspense>
  );
}
