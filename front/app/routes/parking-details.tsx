import { useParams, useNavigate } from "react-router";
import Button from "@/components/atoms/Button";
import { useParking } from "@/hooks/useParkings";
import { useUser } from "@/hooks/useUser";

export default function ParkingDetails() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { data: parking, isLoading } = useParking(id);
  const { data: user } = useUser();

  const handleSubscribe = () => {
    if (!user) {
      navigate("/login");
      return;
    }
    navigate(`/parking/${parking?.id}/subscribe`);
  };

  if (isLoading) return <div className="p-8 text-center">Chargement...</div>;

  if (!parking)
    return <div className="p-8 text-center">Parking non trouvé.</div>;

  return (
    <div className="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div className="lg:col-span-2 space-y-8">
        <div>
          <h1 className="text-3xl font-bold text-secondary mb-2">
            Parking {parking.location}
          </h1>
          <p className="text-tertiary">{parking.location}</p>
        </div>

        <div className="bg-gray-200 h-64 rounded-3xl w-full">
          <div className="w-full h-full flex items-center justify-center text-gray-400">
            Image / Carte
          </div>
        </div>

        <div>
          <h2 className="text-xl font-bold text-secondary mb-4">
            Informations
          </h2>
          <div className="bg-white p-6 rounded-2xl border border-tertiary/20">
            <div className="flex items-center gap-4">
              <div className="text-center">
                <p className="text-3xl font-bold text-secondary">
                  {parking.capacity}
                </p>
                <p className="text-sm text-tertiary">places</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div className="space-y-6">
        <div className="bg-white p-6 rounded-3xl border border-tertiary/20 sticky top-8">
          <h3 className="text-xl font-bold text-secondary mb-6">Tarifs</h3>

          <div className="space-y-4 mb-8">
            {parking.rates && parking.rates.length > 0 ? (
              parking.rates.map((rate) => (
                <div
                  key={rate.id}
                  className="flex justify-between items-center pb-4 border-b border-gray-100"
                >
                  <span className="text-gray-600">{rate.name}</span>
                  <span className="font-bold text-secondary">
                    {rate.amount}€
                  </span>
                </div>
              ))
            ) : (
              <p className="text-tertiary italic">Aucun tarif disponible.</p>
            )}
          </div>

          <Button size="full" onClick={handleSubscribe}>
            S&apos;abonner
          </Button>
          <p className="text-center text-xs text-gray-400 mt-4">
            Annulation gratuite jusqu&apos;à 24h avant
          </p>
        </div>
      </div>
    </div>
  );
}
