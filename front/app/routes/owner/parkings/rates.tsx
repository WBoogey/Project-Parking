import { useState, type FormEvent } from "react";
import { useParams, useNavigate } from "react-router";
import Button from "@/components/atoms/Button";
import InputComplete from "@/components/molecules/InputComplete";
import { useOwnerRates, useCreateRate, useDeleteRate } from "@/hooks/useOwner";
import type { RateType } from "@/types/OwnerTypes";

const RATE_TYPE_OPTIONS: { value: RateType; label: string }[] = [
  { value: "hourly", label: "Horaire" },
  { value: "daily", label: "Journalier" },
  { value: "weekly_subscription", label: "Abonnement hebdomadaire" },
  { value: "monthly_subscription", label: "Abonnement mensuel" },
  { value: "yearly_subscription", label: "Abonnement annuel" },
];

const RATE_LABELS: Record<RateType, string> = {
  hourly: "Horaire",
  daily: "Journalier",
  weekly_subscription: "Hebdomadaire",
  monthly_subscription: "Mensuel",
  yearly_subscription: "Annuel",
};

const CALCULATION_RULES: Record<RateType, string> = {
  hourly: "per_hour",
  daily: "per_day",
  weekly_subscription: "weekly",
  monthly_subscription: "monthly",
  yearly_subscription: "yearly",
};

export default function ManageRates() {
  const { id } = useParams();
  const navigate = useNavigate();
  const parkingId = id || "";

  const { data: rates, isLoading } = useOwnerRates(parkingId);
  const createMutation = useCreateRate(parkingId);
  const deleteMutation = useDeleteRate(parkingId);

  const [showForm, setShowForm] = useState(false);
  const [newRate, setNewRate] = useState({
    type: "hourly" as RateType,
    price: "",
    hourlyDiscount: "",
  });

  const handleSubmit = (e: FormEvent) => {
    e.preventDefault();
    if (!newRate.price) return;

    const discountPercent = newRate.hourlyDiscount
      ? parseFloat(newRate.hourlyDiscount)
      : undefined;
    const discountDecimal =
      discountPercent !== undefined ? discountPercent / 100 : undefined;

    createMutation.mutate(
      {
        type: newRate.type,
        calculationRule: CALCULATION_RULES[newRate.type],
        price: parseFloat(newRate.price),
        hourlyDiscount: discountDecimal,
      },
      {
        onSuccess: () => {
          setShowForm(false);
          setNewRate({
            type: "hourly",
            price: "",
            hourlyDiscount: "",
          });
        },
      },
    );
  };

  const handleDelete = (rateId: string) => {
    deleteMutation.mutate(rateId);
  };

  if (isLoading) {
    return <div className="p-8 text-center">Chargement des tarifs...</div>;
  }

  return (
    <div className="max-w-3xl mx-auto">
      <div className="flex items-center justify-between mb-8">
        <h1 className="text-2xl font-bold text-secondary">
          Gestion des tarifs
        </h1>
        <Button variant="outline" onClick={() => navigate("/owner")}>
          Retour
        </Button>
      </div>

      <div className="bg-white rounded-3xl border border-gray-200 overflow-hidden">
        {rates && rates.length > 0 ? (
          <div className="divide-y divide-gray-100">
            {rates.map((rate) => (
              <div
                key={rate.id}
                className="p-6 flex items-center justify-between"
              >
                <div>
                  <p className="font-bold text-secondary">
                    {RATE_LABELS[rate.type] || rate.type}
                  </p>
                  <p className="text-tertiary text-sm">
                    {rate.price}€
                    {rate.hourlyDiscount !== null &&
                      rate.hourlyDiscount !== undefined &&
                      ` (réduction: ${Math.round(rate.hourlyDiscount * 100)}%)`}
                  </p>
                </div>
                <Button
                  variant="outline"
                  onClick={() => handleDelete(rate.id)}
                  disabled={deleteMutation.isPending}
                >
                  Supprimer
                </Button>
              </div>
            ))}
          </div>
        ) : (
          <div className="p-8 text-center text-tertiary">
            Aucun tarif configuré pour ce parking.
          </div>
        )}
      </div>

      {!showForm ? (
        <div className="mt-6">
          <Button onClick={() => setShowForm(true)}>Ajouter un tarif</Button>
        </div>
      ) : (
        <form
          onSubmit={handleSubmit}
          className="mt-6 bg-white p-8 rounded-3xl border border-gray-200 flex flex-col gap-6"
        >
          <h2 className="text-xl font-bold text-secondary">Nouveau tarif</h2>

          <div className="flex flex-col gap-2">
            <label
              htmlFor="type"
              className="text-sm font-medium text-secondary"
            >
              Type de tarif
            </label>
            <select
              id="type"
              value={newRate.type}
              onChange={(e) =>
                setNewRate({ ...newRate, type: e.target.value as RateType })
              }
              className="px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary"
            >
              {RATE_TYPE_OPTIONS.map((opt) => (
                <option key={opt.value} value={opt.value}>
                  {opt.label}
                </option>
              ))}
            </select>
          </div>

          <InputComplete
            id="price"
            label="Prix (€)"
            placeholder="ex: 3.50"
            type="number"
            variant="full"
            value={newRate.price}
            onChange={(e) => setNewRate({ ...newRate, price: e.target.value })}
            required
          />

          <InputComplete
            id="hourlyDiscount"
            label="Réduction horaire en % (optionnel)"
            placeholder="ex: 15 pour 15%"
            type="number"
            variant="full"
            value={newRate.hourlyDiscount}
            onChange={(e) =>
              setNewRate({ ...newRate, hourlyDiscount: e.target.value })
            }
          />

          <div className="flex justify-end gap-4 mt-4">
            <Button
              variant="outline"
              onClick={() => setShowForm(false)}
              type="button"
            >
              Annuler
            </Button>
            <Button
              onClick={() => {}}
              type="submit"
              disabled={createMutation.isPending}
            >
              {createMutation.isPending ? "Ajout..." : "Ajouter"}
            </Button>
          </div>

          {createMutation.isError && (
            <p className="text-red-500 text-sm">
              Une erreur est survenue lors de l&apos;ajout.
            </p>
          )}
        </form>
      )}
    </div>
  );
}

