import { useNavigate } from "react-router";
import Button from "@/components/atoms/Button";
import {
  useCustomerReservations,
  useCustomerSubscriptions,
  useCustomerStationings,
} from "@/hooks/useCustomer";
import { useCancelSubscription } from "@/hooks/useSubscription";
import {
  useReservations,
  useCancelReservation,
  useGenerateInvoice,
} from "@/hooks/useReservation";
import { useEnterParking, useExitParking } from "@/hooks/useStationing";
import { useParkings } from "@/hooks/useParkings";
import SubscriptionCard from "@/components/molecules/SubscriptionCard";
import type { SubscriptionDetail } from "@/types/SubscriptionTypes";

export default function CustomerDashboard() {
  const navigate = useNavigate();
  const { data: legacyReservations, isLoading: isLoadingLegacyRes, error: legacyError } =
    useCustomerReservations();
  const { data: reservations, isLoading: isLoadingRes, error: resError } = useReservations();
  const { data: subscriptions, isLoading: isLoadingSub, error: subError } =
    useCustomerSubscriptions();
  const { data: stationings, isLoading: isLoadingStat, error: statError } =
    useCustomerStationings();
  const { data: parkings } = useParkings();

  const cancelSubMutation = useCancelSubscription();
  const cancelResMutation = useCancelReservation();
  const invoiceMutation = useGenerateInvoice();
  const enterMutation = useEnterParking();
  const exitMutation = useExitParking();

  const isLoading =
    isLoadingLegacyRes || isLoadingRes || isLoadingSub || isLoadingStat;

  const activeStationing = stationings?.find(
    (s) => s.status === "in_progress" || s.status === "active",
  );

  const getParkingName = (parkingId: string): string => {
    const parking = parkings?.find((p) => p.id === parkingId);
    return parking?.location || `Parking #${parkingId.substring(0, 8)}...`;
  };

  const isReservationActive = (startTime: string, endTime: string): boolean => {
    const now = new Date();
    const start = new Date(startTime);
    const end = new Date(endTime);
    return now >= start && now <= end;
  };

  const canEnterReservation = (
    reservationId: string,
    parkingId: string,
    startTime: string,
    status: string,
  ): boolean => {
    if (activeStationing) return false;
    if (status !== "confirmed") return false;
    const now = new Date();
    const start = new Date(startTime);
    const thirtyMinBefore = new Date(start.getTime() - 30 * 60 * 1000);
    return now >= thirtyMinBefore;
  };

  const handleEnterFromReservation = (parkingId: string) => {
    enterMutation.mutate(parkingId);
  };

  const handleExitParking = (parkingId: string) => {
    exitMutation.mutate(parkingId, {
      onSuccess: (data) => {
        if (data.checkoutUrl) {
          window.location.href = data.checkoutUrl;
        }
      },
    });
  };

  const handleCancelReservation = (id: string) => {
    if (confirm("Voulez-vous vraiment annuler cette réservation ?")) {
      cancelResMutation.mutate(id);
    }
  };

  const handleGenerateInvoice = (id: string) => {
    invoiceMutation.mutate(id, {
      onSuccess: (invoice) => {
        alert(
          `Facture générée : ${invoice.invoiceNumber}\nMontant : ${invoice.formattedAmount}`,
        );
      },
    });
  };

  const handleViewDetails = (id: string) => {
    navigate(`/subscription/${id}`);
  };

  const handleCancelSub = (id: string) => {
    cancelSubMutation.mutate(id);
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
      <div className="bg-gray-50 flex items-center justify-center py-12">
        <p className="text-secondary">Chargement...</p>
      </div>
    );
  }

  const hasErrors = legacyError || resError || subError || statError;

  return (
    <>
      <h1 className="text-2xl font-bold text-secondary mb-8">Mon Espace</h1>

      {hasErrors && (
        <div className="mb-8 p-4 bg-red-50 border border-red-200 rounded-xl">
          <p className="text-red-600">
            Certaines données n'ont pas pu être chargées. Veuillez réessayer.
          </p>
        </div>
      )}

      <div className="grid gap-8">
        {activeStationing && (
          <section className="bg-gradient-to-r from-green-500 to-green-600 p-8 rounded-3xl text-white">
            <h2 className="text-xl font-bold mb-4">Stationnement en cours</h2>
            <div className="bg-white/20 p-4 rounded-2xl mb-4">
              <p className="font-bold text-lg">
                {getParkingName(activeStationing.parkingId)}
              </p>
              <p className="text-sm opacity-80">
                Depuis : {new Date(activeStationing.startTime).toLocaleString()}
              </p>
            </div>
            <Button
              onClick={() => handleExitParking(activeStationing.parkingId)}
              size="full"
              className="bg-white text-green-600 hover:bg-gray-100 text-xl py-6 font-bold"
              disabled={exitMutation.isPending}
            >
              {exitMutation.isPending ? "Sortie en cours..." : "SORTIR DU PARKING"}
            </Button>
          </section>
        )}

        <section>
          <h2 className="text-xl font-bold text-secondary mb-4">
            Mes Réservations
          </h2>
          <div className="grid gap-4">
            {reservations?.map((res) => {
              const isActive = isReservationActive(res.startTime, res.endTime);
              const canEnter = canEnterReservation(
                res.id,
                res.parkingId,
                res.startTime,
                res.status,
              );
              const isCurrentlyParkedHere =
                activeStationing?.parkingId === res.parkingId;

              return (
                <div
                  key={res.id}
                  className={`bg-white p-6 rounded-2xl border-2 ${
                    isActive
                      ? "border-blue-500 shadow-lg"
                      : "border-tertiary/20"
                  }`}
                >
                  <div className="flex justify-between items-start mb-4">
                    <div>
                      <p className="font-bold text-secondary text-lg">
                        {getParkingName(res.parkingId)}
                      </p>
                      <p className="text-tertiary text-sm">
                        Du {new Date(res.startTime).toLocaleString()}
                      </p>
                      <p className="text-tertiary text-sm">
                        Au {new Date(res.endTime).toLocaleString()}
                      </p>
                      {res.amount && (
                        <p className="text-secondary font-medium mt-1">
                          {(res.amount / 100).toFixed(2)}€
                        </p>
                      )}
                    </div>
                    <div
                      className={`px-3 py-1 rounded-full text-xs font-medium ${
                        res.status === "confirmed"
                          ? "bg-green-100 text-green-700"
                          : res.status === "cancelled"
                            ? "bg-red-100 text-red-700"
                            : res.status === "completed"
                              ? "bg-gray-100 text-gray-700"
                              : "bg-yellow-100 text-yellow-700"
                      }`}
                    >
                      {res.status === "confirmed"
                        ? "Confirmé"
                        : res.status === "cancelled"
                          ? "Annulé"
                          : res.status === "completed"
                            ? "Terminé"
                            : "En attente"}
                    </div>
                  </div>

                  <div className="flex flex-wrap gap-2">
                    {canEnter && !isCurrentlyParkedHere && (
                      <Button
                        onClick={() => handleEnterFromReservation(res.parkingId)}
                        className="bg-blue-600 hover:bg-blue-700 text-white font-bold"
                        disabled={enterMutation.isPending}
                      >
                        {enterMutation.isPending ? "..." : "ENTRER"}
                      </Button>
                    )}

                    {isCurrentlyParkedHere && (
                      <Button
                        onClick={() => handleExitParking(res.parkingId)}
                        className="bg-red-600 hover:bg-red-700 text-white font-bold"
                        disabled={exitMutation.isPending}
                      >
                        {exitMutation.isPending ? "..." : "SORTIR"}
                      </Button>
                    )}

                    {res.status === "confirmed" &&
                      !canEnter &&
                      !isCurrentlyParkedHere && (
                        <Button
                          onClick={() => handleCancelReservation(res.id)}
                          variant="outline"
                          size="sm"
                          className="text-red-500 border-red-500"
                          disabled={cancelResMutation.isPending}
                        >
                          Annuler
                        </Button>
                      )}

                    {res.status === "completed" && (
                      <Button
                        onClick={() => handleGenerateInvoice(res.id)}
                        variant="outline"
                        size="sm"
                        disabled={invoiceMutation.isPending}
                      >
                        {invoiceMutation.isPending ? "..." : "Facture"}
                      </Button>
                    )}
                  </div>
                </div>
              );
            })}

            {legacyReservations?.map((res) => (
              <div
                key={res.id}
                className="bg-white p-6 rounded-2xl border border-tertiary/20 flex justify-between items-center"
              >
                <div>
                  <p className="font-bold text-secondary">
                    {getParkingName(res.parkingId)}
                  </p>
                  <p className="text-tertiary">
                    {res.dayOfWeek} • {res.startHour} - {res.endHour}
                  </p>
                </div>
                <div className="px-4 py-2 bg-green-100 text-green-700 rounded-xl text-sm font-medium">
                  Confirmé
                </div>
              </div>
            ))}

            {(!reservations || reservations.length === 0) &&
              (!legacyReservations || legacyReservations.length === 0) && (
                <div className="text-center py-8">
                  <p className="text-tertiary mb-4">Aucune réservation.</p>
                  <Button onClick={() => navigate("/search")} variant="outline">
                    Rechercher un parking
                  </Button>
                </div>
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
                onCancel={handleCancelSub}
                isLoading={cancelSubMutation.isPending}
              />
            ))}
            {(!subscriptions || subscriptions.length === 0) && (
              <p className="text-tertiary italic">Aucun abonnement.</p>
            )}
          </div>
        </section>

        <section>
          <h2 className="text-xl font-bold text-secondary mb-4">
            Historique Stationnements
          </h2>
          <div className="grid gap-4">
            {stationings
              ?.filter((s) => s.status !== "in_progress" && s.status !== "active")
              .map((stat) => (
                <div
                  key={stat.id}
                  className="bg-white p-6 rounded-2xl border border-tertiary/20 flex justify-between items-center"
                >
                  <div>
                    <p className="font-bold text-secondary">
                      {getParkingName(stat.parkingId)}
                    </p>
                    <p className="text-tertiary text-sm">
                      Entrée : {new Date(stat.startTime).toLocaleString()}
                    </p>
                    {stat.endTime && (
                      <p className="text-tertiary text-sm">
                        Sortie : {new Date(stat.endTime).toLocaleString()}
                      </p>
                    )}
                  </div>
                  <div className="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium">
                    Terminé
                  </div>
                </div>
              ))}
            {stationings?.filter(
              (s) => s.status !== "in_progress" && s.status !== "active",
            ).length === 0 && (
              <p className="text-tertiary italic">Aucun stationnement passé.</p>
            )}
          </div>
        </section>
      </div>
    </>
  );
}
