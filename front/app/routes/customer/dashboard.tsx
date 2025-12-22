import { useNavigate } from "react-router";
import { toast } from "sonner";
import Button from "@/components/atoms/Button";
import { SkeletonCard } from "@/components/atoms/Skeleton";
import EmptyState from "@/components/atoms/EmptyState";
import {
  useCustomerReservations,
  useCustomerSubscriptions,
  useCustomerStationings,
} from "@/hooks/useCustomer";
import { useCancelSubscription } from "@/hooks/useSubscription";
import {
  useCancelReservation,
  useGenerateInvoice,
} from "@/hooks/useReservation";
import { useEnterParking, useExitParking } from "@/hooks/useStationing";
import { useParkings } from "@/hooks/useParkings";
import SubscriptionCard from "@/components/molecules/SubscriptionCard";
import type { SubscriptionDetail } from "@/types/SubscriptionTypes";

export default function CustomerDashboard() {
  const navigate = useNavigate();
  const {
    data: reservations,
    isLoading: isLoadingRes,
    error: resError,
  } = useCustomerReservations();
  const {
    data: subscriptions,
    isLoading: isLoadingSub,
    error: subError,
  } = useCustomerSubscriptions();
  const {
    data: stationings,
    isLoading: isLoadingStat,
    error: statError,
  } = useCustomerStationings();
  const { data: parkings } = useParkings();

  const cancelSubMutation = useCancelSubscription();
  const cancelResMutation = useCancelReservation();
  const invoiceMutation = useGenerateInvoice();
  const enterMutation = useEnterParking();
  const exitMutation = useExitParking();

  const isLoading = isLoadingRes || isLoadingSub || isLoadingStat;

  const activeStationing = stationings?.find(
    (s) => s.status === "in_progress" || s.status === "active",
  );

  const getReservationForStationing = () => {
    if (!activeStationing || !reservations) return null;
    return reservations.find(
      (r) =>
        r.parkingId === activeStationing.parkingId &&
        (r.status === "confirmed" || r.status === "pending"),
    );
  };

  const getOvertimeInfo = () => {
    if (!activeStationing) return null;
    const reservation = getReservationForStationing();
    if (!reservation) return null;

    const now = new Date();
    const reservationEnd = new Date(reservation.endTime);
    const diffMs = now.getTime() - reservationEnd.getTime();

    if (diffMs <= 0) {
      const remainingMs = reservationEnd.getTime() - now.getTime();
      const remainingMin = Math.ceil(remainingMs / 60000);
      if (remainingMin <= 15) {
        return {
          isOvertime: false,
          isWarning: true,
          message: `Attention : ${remainingMin} min restantes avant dépassement`,
          remainingMinutes: remainingMin,
        };
      }
      return null;
    }

    const overtimeMin = Math.ceil(diffMs / 60000);
    return {
      isOvertime: true,
      isWarning: false,
      message: `Dépassement de ${overtimeMin} min - Pénalité de 20€ applicable`,
      overtimeMinutes: overtimeMin,
    };
  };

  const overtimeInfo = getOvertimeInfo();

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
    parkingId: string,
    startTime: string,
    endTime: string,
    status: string,
  ): boolean => {
    if (activeStationing) return false;
    if (status !== "confirmed" && status !== "pending") return false;
    const now = new Date();
    const start = new Date(startTime);
    const end = new Date(endTime);
    const thirtyMinBefore = new Date(start.getTime() - 30 * 60 * 1000);
    return now >= thirtyMinBefore && now <= end;
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
    cancelResMutation.mutate(id, {
      onSuccess: () => {
        toast.success("Réservation annulée");
      },
      onError: () => {
        toast.error("Erreur lors de l'annulation");
      },
    });
  };

  const handleGenerateInvoice = (id: string) => {
    invoiceMutation.mutate(id, {
      onSuccess: (invoice) => {
        toast.success(`Facture ${invoice.invoiceNumber} générée`, {
          description: `Montant : ${invoice.formattedAmount}`,
        });
      },
      onError: () => {
        toast.error("Erreur lors de la génération de la facture");
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
      <div className="flex flex-col gap-8">
        <div className="h-8 w-48 bg-gray-200 rounded-xl animate-pulse" />
        <div className="grid gap-4">
          <SkeletonCard />
          <SkeletonCard />
          <SkeletonCard />
        </div>
      </div>
    );
  }

  const hasErrors = resError || subError || statError;

  return (
    <>
      <h1 className="text-2xl font-bold text-secondary mb-8">Mon Espace</h1>

      {hasErrors && (
        <div className="mb-8 p-4 bg-red-50 border border-red-200 rounded-xl">
          <p className="text-red-600">
            Certaines données n&apos;ont pas pu être chargées. Veuillez
            réessayer.
          </p>
        </div>
      )}

      <div className="grid gap-8">
        {activeStationing && (
          <section
            className={`p-8 rounded-3xl text-white ${
              overtimeInfo?.isOvertime
                ? "bg-linear-to-r from-red-500 to-red-600"
                : overtimeInfo?.isWarning
                  ? "bg-linear-to-r from-orange-500 to-orange-600"
                  : "bg-linear-to-r from-green-500 to-green-600"
            }`}
          >
            <h2 className="text-xl font-bold mb-4">Stationnement en cours</h2>

            {overtimeInfo && (
              <div
                className={`p-4 rounded-2xl mb-4 flex items-center gap-3 ${
                  overtimeInfo.isOvertime ? "bg-white/30" : "bg-white/20"
                }`}
              >
                <span className="text-2xl">
                  {overtimeInfo.isOvertime ? "⚠️" : "⏰"}
                </span>
                <div>
                  <p className="font-bold">{overtimeInfo.message}</p>
                  {overtimeInfo.isOvertime && (
                    <p className="text-sm opacity-90">
                      Sortez rapidement pour limiter les frais supplémentaires
                    </p>
                  )}
                </div>
              </div>
            )}

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
              className={`text-xl py-6 font-bold ${
                overtimeInfo?.isOvertime
                  ? "bg-white text-red-600 hover:bg-gray-100"
                  : "bg-white text-green-600 hover:bg-gray-100"
              }`}
              disabled={exitMutation.isPending}
            >
              {exitMutation.isPending
                ? "Sortie en cours..."
                : "SORTIR DU PARKING"}
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
                res.parkingId,
                res.startTime,
                res.endTime,
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
                        onClick={() =>
                          handleEnterFromReservation(res.parkingId)
                        }
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

            {(!reservations || reservations.length === 0) && (
              <EmptyState
                icon="🅿️"
                title="Aucune réservation"
                description="Vous n'avez pas encore de réservation."
                action={
                  <Button onClick={() => navigate("/search")} variant="outline">
                    Rechercher un parking
                  </Button>
                }
              />
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
              <EmptyState
                icon="📋"
                title="Aucun abonnement"
                description="Abonnez-vous pour profiter de tarifs avantageux."
              />
            )}
          </div>
        </section>

        <section>
          <h2 className="text-xl font-bold text-secondary mb-4">
            Historique Stationnements
          </h2>
          <div className="grid gap-4">
            {stationings
              ?.filter(
                (s) => s.status !== "in_progress" && s.status !== "active",
              )
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
              <EmptyState
                icon="🚗"
                title="Aucun stationnement passé"
                description="Votre historique de stationnement apparaîtra ici."
              />
            )}
          </div>
        </section>
      </div>
    </>
  );
}
