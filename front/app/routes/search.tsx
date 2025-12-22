import { useState } from "react";
import { useNavigate } from "react-router";
import Button from "@/components/atoms/Button";
import InputComplete from "@/components/molecules/InputComplete";
import ParkingCard from "@/components/organisms/ParkingCard";
import { SkeletonCard } from "@/components/atoms/Skeleton";
import EmptyState from "@/components/atoms/EmptyState";
import { useParkings } from "@/hooks/useParkings";
import { useDebounce } from "@/hooks/useDebounce";

export default function Search() {
  const navigate = useNavigate();
  const [searchTerm, setSearchTerm] = useState("");
  const debouncedSearch = useDebounce(searchTerm, 300);

  const { data: parkings, isLoading } = useParkings(debouncedSearch);

  const getMinPrice = (rates: { price: number }[]) => {
    if (!rates || rates.length === 0) return null;
    const min = Math.min(...rates.map((r) => r.price));
    return `${min}€`;
  };

  return (
    <div className="max-w-4xl mx-auto flex flex-col gap-8">
      <div className="text-center space-y-4">
        <h1 className="text-3xl font-bold text-secondary">
          Trouvez votre place de parking
        </h1>
        <p className="text-tertiary">Réservez en quelques clics</p>
      </div>

      <div className="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <div className="flex gap-4">
          <InputComplete
            id="search"
            label=""
            placeholder="Où cherchez-vous ?"
            variant="full"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="flex-1"
          />
          <Button onClick={() => {}} className="mt-1.5">
            Rechercher
          </Button>
        </div>
      </div>

      {isLoading ? (
        <div className="grid gap-4">
          {[0, 1, 2].map((i) => (
            <SkeletonCard key={i} />
          ))}
        </div>
      ) : (
        <div className="grid gap-4">
          {parkings?.map((parking, index) => (
            <div
              key={parking.id}
              className={`cursor-pointer animate-fade-in-up ${
                index === 0
                  ? ""
                  : index === 1
                    ? "animation-delay-100"
                    : index === 2
                      ? "animation-delay-200"
                      : index === 3
                        ? "animation-delay-300"
                        : "animation-delay-400"
              }`}
              style={{ opacity: 0 }}
              onClick={() => navigate(`/parking/${parking.id}`)}
            >
              <ParkingCard
                name={parking.location}
                totalSpots={parking.capacity}
                price={getMinPrice(parking.rates) || undefined}
                onEdit={() => navigate(`/parking/${parking.id}`)}
                editLabel="Voir"
                className="hover:border-secondary transition-colors"
              />
            </div>
          ))}
          {parkings?.length === 0 && (
            <EmptyState
              icon="🔍"
              title="Aucun résultat"
              description="Aucun parking ne correspond à votre recherche."
            />
          )}
        </div>
      )}
    </div>
  );
}
