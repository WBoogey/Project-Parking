interface SkeletonProps {
  className?: string;
}

export default function Skeleton({ className = "" }: SkeletonProps) {
  return (
    <div className={`animate-pulse bg-gray-200 rounded-xl ${className}`} />
  );
}

export function SkeletonCard() {
  return (
    <div className="bg-white p-6 rounded-2xl border border-gray-200 animate-fade-in-up">
      <Skeleton className="h-6 w-3/4 mb-4" />
      <Skeleton className="h-4 w-1/2 mb-2" />
      <Skeleton className="h-4 w-1/3 mb-4" />
      <Skeleton className="h-10 w-full" />
    </div>
  );
}

export function SkeletonParkingDetails() {
  return (
    <div className="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div className="lg:col-span-2 space-y-8">
        <div>
          <Skeleton className="h-10 w-3/4 mb-4" />
          <Skeleton className="h-5 w-1/2" />
        </div>
        <Skeleton className="h-64 w-full rounded-3xl" />
        <div>
          <Skeleton className="h-8 w-1/4 mb-4" />
          <div className="bg-white p-6 rounded-2xl border border-gray-200">
            <Skeleton className="h-12 w-24" />
          </div>
        </div>
      </div>
      <div>
        <div className="bg-white p-6 rounded-3xl border border-gray-200">
          <Skeleton className="h-8 w-1/2 mb-6" />
          <Skeleton className="h-6 w-full mb-4" />
          <Skeleton className="h-6 w-full mb-4" />
          <Skeleton className="h-6 w-full mb-8" />
          <Skeleton className="h-12 w-full" />
        </div>
      </div>
    </div>
  );
}
