import { useState } from "react";
import { useNavigate } from "react-router";
import Button from "@/components/atoms/Button";
import InputComplete from "@/components/molecules/InputComplete";
import ParkingCard from "@/components/organisms/ParkingCard";
import { useParkings } from "@/hooks/useParkings";

export default function Search() {
  const navigate = useNavigate();
  const [searchTerm, setSearchTerm] = useState("");

  const { data: parkings, isLoading } = useParkings(searchTerm);

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
        <div className="text-center py-8">Chargement des parkings...</div>
      ) : (
        <div className="grid gap-4">
          {parkings?.map((parking) => (
            <div
              key={parking.id}
              className="cursor-pointer"
              onClick={() => navigate(`/parking/${parking.id}`)}
            >
              <ParkingCard
                name={parking.location}
                totalSpots={parking.capacity}
                availableSpots={parking.available}
                price={parking.priceDisplay}
                onEdit={() => navigate(`/parking/${parking.id}`)}
                editLabel="Voir"
                className="hover:border-secondary transition-colors"
              />
            </div>
          ))}
          {parkings?.length === 0 && (
            <div className="text-center py-8 text-tertiary">
              Aucun parking trouvé pour cette recherche.
            </div>
          )}
        </div>
      )}
    </div>
  );
}
