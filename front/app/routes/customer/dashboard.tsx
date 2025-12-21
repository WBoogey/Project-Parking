import { useNavigate } from "react-router";
import {
  useCustomerReservations,
  useCustomerSubscriptions,
  useCustomerStationings,
} from "@/hooks/useCustomer";
import { useCancelSubscription } from "@/hooks/useSubscription";
import SubscriptionCard from "@/components/molecules/SubscriptionCard";
import type { SubscriptionDetail } from "@/types/SubscriptionTypes";

export default function CustomerDashboard() {
  const navigate = useNavigate();
  const { data: reservations, isLoading: isLoadingRes } =
    useCustomerReservations();
  const { data: subscriptions, isLoading: isLoadingSub } =
    useCustomerSubscriptions();
  const { data: stationings, isLoading: isLoadingStat } =
    useCustomerStationings();

  const cancelMutation = useCancelSubscription();

  const isLoading = isLoadingRes || isLoadingSub || isLoadingStat;

  const handleViewDetails = (id: string) => {
    navigate(`/subscription/${id}`);
  };

  const handleCancel = (id: string) => {
    cancelMutation.mutate(id);
  };

  const mapToSubscriptionDetail = (sub: {
    id: string;
    startDate: string;
    endDate: string;
    rate: { id: string; name: string; amount: number };
    parkingId: string;
  }): SubscriptionDetail => {
    let subscriptionType: SubscriptionDetail["subscriptionType"] = "custom";
    const rateName = sub.rate.name.toLowerCase();
    if (rateName.includes("total") || rateName.includes("24")) {
      subscriptionType = "total";
    } else if (rateName.includes("week") || rateName.includes("end")) {
      subscriptionType = "weekend";
    } else if (rateName.includes("soir") || rateName.includes("evening")) {
      subscriptionType = "evening";
    }

    return {
      id: sub.id,
      userId: "",
      parkingId: sub.parkingId,
      subscriptionType,
      startDate: sub.startDate,
      endDate: sub.endDate,
      weeklySlots: [],
      status: new Date(sub.endDate) > new Date() ? "active" : "expired",
      monthlyPrice: sub.rate.amount,
    };
  };

  if (isLoading) {
    return (
      <div className="bg-gray-50 flex items-center justify-center">
        <p className="text-secondary">Chargement...</p>
      </div>
    );
  }

  return (
    <>
      <h1 className="text-2xl font-bold text-secondary mb-8">Mon Espace</h1>

      <div className="grid gap-8">
        <section>
          <h2 className="text-xl font-bold text-secondary mb-4">
            Mes Réservations
          </h2>
          <div className="grid gap-4">
            {reservations?.map((res) => (
              <div
                key={res.id}
                className="bg-white p-6 rounded-2xl border border-tertiary/20 flex justify-between items-center"
              >
                <div>
                  <p className="font-bold text-secondary">
                    Parking #{res.parkingId.substring(0, 8)}...
                  </p>
                  <p className="text-tertiary">
                    {res.dayOfWeek} • {res.startHour} - {res.endHour}
                  </p>
                </div>
                <div className="px-4 py-2 bg-available/20 text-green-700 rounded-xl text-sm font-medium">
                  Confirmé
                </div>
              </div>
            ))}
            {reservations?.length === 0 && (
              <p className="text-tertiary italic">Aucune réservation.</p>
            )}
          </div>
        </section>

        <section>
          <h2 className="text-xl font-bold text-secondary mb-4">
            Mes Abonnements
          </h2>
          <div className="grid gap-4">
            {subscriptions?.map((sub) => (
              <SubscriptionCard
                key={sub.id}
                subscription={mapToSubscriptionDetail(sub)}
                onViewDetails={handleViewDetails}
                onCancel={handleCancel}
                isLoading={cancelMutation.isPending}
              />
            ))}
            {subscriptions?.length === 0 && (
              <p className="text-tertiary italic">Aucun abonnement.</p>
            )}
          </div>
        </section>

        <section>
          <h2 className="text-xl font-bold text-secondary mb-4">
            Mes Stationnements
          </h2>
          <div className="grid gap-4">
            {stationings?.map((stat) => (
              <div
                key={stat.id}
                className="bg-white p-6 rounded-2xl border border-tertiary/20 flex justify-between items-center"
              >
                <div>
                  <p className="font-bold text-secondary">
                    Parking #{stat.parkingId.substring(0, 8)}...
                  </p>
                  <p className="text-tertiary">
                    Entrée : {new Date(stat.startTime).toLocaleString()}
                  </p>
                  {stat.endTime && (
                    <p className="text-tertiary">
                      Sortie : {new Date(stat.endTime).toLocaleString()}
                    </p>
                  )}
                </div>
                <div
                  className={`px-4 py-2 rounded-xl text-sm font-medium ${
                    stat.status === "in_progress"
                      ? "bg-yellow-100 text-yellow-700"
                      : "bg-gray-100 text-gray-700"
                  }`}
                >
                  {stat.status === "in_progress" ? "En cours" : stat.status}
                </div>
              </div>
            ))}
            {stationings?.length === 0 && (
              <p className="text-tertiary italic">Aucun stationnement.</p>
            )}
          </div>
        </section>
      </div>
    </>
  );
}
