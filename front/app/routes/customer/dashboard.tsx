import { useQuery } from "@tanstack/react-query";
import apiClient from "@/api/client";
import Navbar from "@/components/organisms/Navbar";

interface Reservation {
  id: string;
  dayOfWeek: string;
  startHour: string;
  endHour: string;
  parkingId: string;
}

interface Subscription {
  id: string;
  startDate: string;
  endDate: string;
  rate: {
    id: string;
    name: string;
    amount: number;
  };
  parkingId: string;
}

export default function CustomerDashboard() {
  const { data: reservations, isLoading: isLoadingRes } = useQuery({
    queryKey: ["customer", "reservations"],
    queryFn: async () => {
      const response = await apiClient.get<{ data: Reservation[] }>(
        "/customer/reservations",
      );
      return response.data.data;
    },
  });

  const { data: subscriptions, isLoading: isLoadingSub } = useQuery({
    queryKey: ["customer", "subscriptions"],
    queryFn: async () => {
      const response = await apiClient.get<{ data: Subscription[] }>(
        "/customer/subscriptions",
      );
      return response.data.data;
    },
  });

  const isLoading = isLoadingRes || isLoadingSub;

  if (isLoading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <p className="text-secondary">Chargement...</p>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <Navbar />
      <main className="container mx-auto py-8 px-4">
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
                <div
                  key={sub.id}
                  className="bg-white p-6 rounded-2xl border border-tertiary/20 flex justify-between items-center"
                >
                  <div>
                    <p className="font-bold text-secondary">
                      Parking #{sub.parkingId.substring(0, 8)}...
                    </p>
                    <p className="text-tertiary">
                      Du {new Date(sub.startDate).toLocaleDateString()} au{" "}
                      {new Date(sub.endDate).toLocaleDateString()}
                    </p>
                  </div>
                  <div className="px-4 py-2 bg-accent/10 text-accent rounded-xl text-sm font-medium">
                    Actif
                  </div>
                </div>
              ))}
              {subscriptions?.length === 0 && (
                <p className="text-tertiary italic">Aucun abonnement.</p>
              )}
            </div>
          </section>
        </div>
      </main>
    </div>
  );
}
