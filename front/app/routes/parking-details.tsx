import { useParams, useNavigate } from "react-router";
import Button from "@/components/atoms/Button";

export default function ParkingDetails() {
  const { id } = useParams();
  const navigate = useNavigate();

  const parking = {
    id,
    location: "12 Rue de la Paix, 75000 Paris",
    description:
      "Parking sécurisé en plein centre de Paris. Accès 24/7, vidéosurveillance.",
    features: ["Sécurisé", "Couvert", "Accès handicapé", "Bornes électriques"],
    pricing: {
      hourly: 2.5,
      daily: 15,
      monthly: 150,
    },
  };

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
          <h2 className="text-xl font-bold text-secondary mb-4">Description</h2>
          <p className="text-gray-600 leading-relaxed">{parking.description}</p>
        </div>

        <div>
          <h2 className="text-xl font-bold text-secondary mb-4">Services</h2>
          <div className="flex flex-wrap gap-2">
            {parking.features.map((f) => (
              <span
                key={f}
                className="bg-white border border-tertiary/30 px-4 py-2 rounded-full text-secondary text-sm"
              >
                {f}
              </span>
            ))}
          </div>
        </div>
      </div>

      <div className="space-y-6">
        <div className="bg-white p-6 rounded-3xl border border-tertiary/20 sticky top-8">
          <h3 className="text-xl font-bold text-secondary mb-6">Tarifs</h3>

          <div className="space-y-4 mb-8">
            <div className="flex justify-between items-center pb-4 border-b border-gray-100">
              <span className="text-gray-600">1 Heure</span>
              <span className="font-bold text-secondary">
                {parking.pricing.hourly}€
              </span>
            </div>
            <div className="flex justify-between items-center pb-4 border-b border-gray-100">
              <span className="text-gray-600">1 Journée</span>
              <span className="font-bold text-secondary">
                {parking.pricing.daily}€
              </span>
            </div>
            <div className="flex justify-between items-center pb-4 border-b border-gray-100">
              <span className="text-gray-600">1 Mois</span>
              <span className="font-bold text-secondary">
                {parking.pricing.monthly}€
              </span>
            </div>
          </div>

          <Button size="full" onClick={() => navigate("/payment")}>
            Réserver maintenant
          </Button>
          <p className="text-center text-xs text-gray-400 mt-4">
            Annulation gratuite jusqu&apos;à 24h avant
          </p>
        </div>
      </div>
    </div>
  );
}
