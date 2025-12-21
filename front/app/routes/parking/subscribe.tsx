import { useState } from "react";
import { useParams, useNavigate } from "react-router";
import Button from "@/components/atoms/Button";
import SubscriptionTypeSelector from "@/components/molecules/SubscriptionTypeSelector";
import WeeklySlotPicker from "@/components/molecules/WeeklySlotPicker";
import SelectInput from "@/components/molecules/SelectInput/SelectInput";
import StripePayment from "@/components/organisms/StripePayment";
import { useParking } from "@/hooks/useParkings";
import { useUser } from "@/hooks/useUser";
import { useSubscriptionPrice } from "@/hooks/useSubscription";
import { useCreateCheckoutSession } from "@/hooks/useStripe";
import type { SubscriptionType, WeeklySlot } from "@/types/SubscriptionTypes";
import { getDefaultSlotsForType } from "@/api/subscriptionApi";

const DURATION_OPTIONS = ["1 mois", "2 mois", "3 mois", "6 mois", "12 mois"];

const parseDuration = (value: string): number => {
  const match = value.match(/(\d+)/);
  return match ? parseInt(match[1], 10) : 1;
};

export default function SubscribePage() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { data: parking, isLoading: isParkingLoading } = useParking(id);
  const { data: user } = useUser();

  const [subscriptionType, setSubscriptionType] =
    useState<SubscriptionType>("total");
  const [durationString, setDurationString] = useState("1 mois");
  const [startDate, setStartDate] = useState(() => {
    const today = new Date();
    return today.toISOString().split("T")[0];
  });
  const [customSlots, setCustomSlots] = useState<WeeklySlot[]>([]);

  const durationMonths = parseDuration(durationString);

  const { data: priceData } = useSubscriptionPrice(
    id ?? "",
    subscriptionType,
    durationMonths,
    subscriptionType === "custom" ? customSlots : undefined,
  );

  const checkoutMutation = useCreateCheckoutSession();

  const handleSubscribe = () => {
    if (!user) {
      navigate("/login");
      return;
    }

    if (!id) return;

    const slots =
      subscriptionType === "custom"
        ? customSlots
        : getDefaultSlotsForType(subscriptionType);

    checkoutMutation.mutate({
      parkingId: id,
      subscriptionType,
      startDate,
      durationMonths,
      weeklySlots: slots,
    });
  };

  const isFormValid = () => {
    if (!startDate) return false;
    if (subscriptionType === "custom" && customSlots.length === 0) return false;
    return true;
  };

  if (isParkingLoading) {
    return (
      <div className="flex items-center justify-center">
        <p className="text-secondary">Chargement...</p>
      </div>
    );
  }

  if (!parking) {
    return (
      <div className="flex items-center justify-center">
        <p className="text-secondary">Parking non trouvé.</p>
      </div>
    );
  }

  if (checkoutMutation.isPending) {
    return (
      <div className="max-w-3xl mx-auto">
        <StripePayment isLoading={true} />
      </div>
    );
  }

  return (
    <div className="max-w-3xl mx-auto">
      <button
        type="button"
        onClick={() => navigate(-1)}
        className="text-tertiary hover:text-secondary mb-6 flex items-center gap-2 cursor-pointer"
      >
        ← Retour
      </button>

      <h1 className="text-3xl font-bold text-secondary mb-2">S&apos;abonner</h1>
      <p className="text-tertiary mb-8">Parking {parking.location}</p>

      <div className="bg-white rounded-3xl border border-tertiary/20 p-8 space-y-8">
        <SubscriptionTypeSelector
          value={subscriptionType}
          onChange={setSubscriptionType}
        />

        {subscriptionType === "custom" && (
          <WeeklySlotPicker value={customSlots} onChange={setCustomSlots} />
        )}

        <div className="flex flex-col gap-3">
          <label className="font-semibold text-secondary font-inter">
            Durée de l&apos;abonnement
          </label>
          <SelectInput
            placeholder="Sélectionnez une durée"
            choices={DURATION_OPTIONS}
            value={durationString}
            onChange={setDurationString}
            variant="full"
          />
        </div>

        <div className="flex flex-col gap-3">
          <label
            htmlFor="startDate"
            className="font-semibold text-secondary font-inter"
          >
            Date de début
          </label>
          <input
            type="date"
            id="startDate"
            value={startDate}
            onChange={(e) => setStartDate(e.target.value)}
            min={new Date().toISOString().split("T")[0]}
            className="w-full px-4 py-3 rounded-xl border border-tertiary/30 text-secondary focus:outline-secondary cursor-pointer"
          />
        </div>

        <div className="bg-gray-50 rounded-2xl p-6">
          <h3 className="font-semibold text-secondary mb-4">Récapitulatif</h3>
          <div className="space-y-2 text-sm">
            <div className="flex justify-between">
              <span className="text-tertiary">Type</span>
              <span className="text-secondary font-medium">
                {subscriptionType === "total" && "Total (24/7)"}
                {subscriptionType === "weekend" && "Week-end"}
                {subscriptionType === "evening" && "Soir"}
                {subscriptionType === "custom" && "Personnalisé"}
              </span>
            </div>
            <div className="flex justify-between">
              <span className="text-tertiary">Durée</span>
              <span className="text-secondary font-medium">
                {durationString}
              </span>
            </div>
            <div className="flex justify-between">
              <span className="text-tertiary">Début</span>
              <span className="text-secondary font-medium">
                {new Date(startDate).toLocaleDateString("fr-FR")}
              </span>
            </div>
            <div className="h-px bg-tertiary/20 my-3" />
            <div className="flex justify-between">
              <span className="text-tertiary">Prix mensuel</span>
              <span className="text-secondary font-bold">
                {priceData?.monthlyPrice ?? 0}€/mois
              </span>
            </div>
            <div className="flex justify-between text-lg">
              <span className="text-secondary font-semibold">Total</span>
              <span className="text-accent font-bold">
                {priceData?.totalPrice ?? 0}€
              </span>
            </div>
          </div>
        </div>

        {checkoutMutation.isError && (
          <StripePayment
            isLoading={false}
            error={checkoutMutation.error}
            onRetry={handleSubscribe}
          />
        )}

        <Button
          size="full"
          onClick={handleSubscribe}
          disabled={!isFormValid() || checkoutMutation.isPending}
        >
          Payer avec Stripe
        </Button>
      </div>
    </div>
  );
}
